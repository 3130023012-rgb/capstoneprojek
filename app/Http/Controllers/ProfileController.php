<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User; // Asumsi Model User Anda ada
use App\Models\Member; // Asumsi Model Member Anda ada

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir untuk mengedit profil pengguna yang sedang login.
     */
    public function edit(Request $request)
    {
        // Mendapatkan data User yang sedang login
        $user = Auth::user();

        // Mengambil data Member yang berelasi dengan User
        // Asumsi Model User memiliki relasi hasOne('App\Models\Member')
        $member = $user->member; 

        if (!$member) {
            // Handle jika admin/user tidak memiliki record di tabel members
            return redirect()->route('dashboard.index')->with('error', 'Data biodata Anda belum lengkap.');
        }

        // Mengirimkan data User dan Member ke view
        return view('profile.edit', compact('user', 'member'));
    }

    /**
     * Mengupdate data profil (nama, email, dan biodata Member).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi input
        $validatedUser = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Tambahkan validasi untuk data Member (biodata)
            'study_program' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:15',
            // ... (validasi kolom member lainnya)
        ]);

        // 2. Update data User
        $user->update([
            'name' => $validatedUser['name'],
            'email' => $validatedUser['email'],
        ]);

        // 3. Update data Member (Biodata)
        if ($user->member) {
            $user->member->update([
                'study_program' => $validatedUser['study_program'],
                'phone_number' => $validatedUser['phone_number'],
                // ... (update kolom member lainnya)
            ]);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diupdate!');
    }
}