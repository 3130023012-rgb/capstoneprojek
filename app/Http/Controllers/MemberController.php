<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class MemberController extends Controller
{
    /**
     * Menampilkan daftar semua anggota (Biodata).
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        
        $members = Member::with(['user.role']) // <<< Memuat user dan relasi role dari user
        ->when($searchTerm, function ($query, $searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('member_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('study_program', 'like', '%' . $searchTerm . '%');
        })
        ->orderBy('name', 'asc')
        ->get();

        return view('member_list', [
            'members' => $members,
            'searchTerm' => $searchTerm,
        ]);
    }

    // ----------------------------------------------------
    // CRUD BIODATA MEMBER
    // ----------------------------------------------------

    public function create()
    {
        return view('member_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => 'nullable|string|max:50|unique:members,member_id',
            'study_program' => 'nullable|string|max:255',
        ]);

        Member::create($validated);

        return redirect()->route('member.index')->with('success', 'Data Anggota baru berhasil ditambahkan!');
    }

    public function edit(Member $member)
    {
        return view('member_edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => ['nullable', 'string', 'max:50', Rule::unique('members')->ignore($member->id)],
            'study_program' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
        ]);

        $member->update($validated);

        return redirect()->route('member.profile', $member)->with('success', 'Data anggota berhasil diperbarui!');
    }

    /**
     * [BARU] Menghapus data anggota dan akun user terkait.
     */
    public function destroy(Member $member)
    {
        DB::transaction(function () use ($member) {
            // Hapus akun User yang terhubung (jika ada)
            if ($member->user) {
                $member->user->delete();
            }
            // Hapus data Member itu sendiri
            $member->delete();
        });

        return redirect()->route('member.index')->with('success', 'Data anggota berhasil dihapus, termasuk akun loginnya!');
    }
    
    // ----------------------------------------------------
    // PROFIL & RIWAYAT KEHADIRAN
    // ----------------------------------------------------

    public function profile(Member $member)
    {
        $recentAttendances = Attendance::where('member_id', $member->id)
            ->with('activity.trainer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('member_profile', compact('member', 'recentAttendances'));
    }

    // ----------------------------------------------------
    // MANAJEMEN AKUN LOGIN
    // ----------------------------------------------------

    public function createUserForm(Request $request)
    {
        // Ambil semua role dari database
        $roles = Role::all();
        
        // Kirimkan daftar roles ke view
        return view('member_user_create', compact('roles'));
    }

    /**
     * Menyimpan akun User baru dengan Role yang dipilih.
     * Tidak lagi terhubung otomatis ke tabel 'members' di sini.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            // Input nama wajib karena tidak diambil dari dropdown member
            'name' => 'required|string|max:255', 
            'role_id' => 'required|exists:roles,id', // Validasi role yang dipilih
            'username' => 'required|string|min:4|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            // Email dibuat dummy karena tidak ada data member
        ]);

        // Karena ini adalah form general untuk membuat akun, kita langsung buat user.
        // TIDAK ADA KONEKSI OTOMATIS KE MEMBER DARI FORM INI.
        
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => strtolower($validated['username']) . '@pagarnusa.id', // Email dummy
            'password' => ($validated['password']), // Hashing aman
            'role_id' => $validated['role_id'], // Role yang dipilih dari form
        ]);

        // Redirect ke daftar member, atau daftar user jika Anda punya
        return redirect()->route('member.index')->with('success', "Akun {$validated['name']} (Role ID {$validated['role_id']}) berhasil dibuat!");
    }
}
