<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tetap perlu untuk Auth::login() dan logout
use App\Models\User; 

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        return view('login'); 
    }

    /**
     * Menentukan kolom mana yang akan digunakan sebagai username.
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Memproses percobaan login (Menggunakan Logika Plaintext yang Tidak Aman).
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'username' => ['required', 'string'], 
            'password' => ['required', 'string'],
        ]);

        // ====================================================================
        // !!! KODE TIDAK AMAN: Mencari dan Verifikasi Password Plaintext !!!
        // ====================================================================

        // Cari user berdasarkan username
        // Perlu import model User di atas jika Anda ingin menggunakan User::where
        $user = User::where('username', $credentials['username'])->first(); 

        // Bandingkan password plaintext (INI SANGAT TIDAK AMAN)
        if ($user && $user->password === $credentials['password']) {
            
            Auth::login($user); // Login user secara manual
            $request->session()->regenerate();

            // 2. Redireksi Berdasarkan Peran
            $successMessage = 'Percobaan Login Berhasil! (PERINGATAN: Keamanan dinonaktifkan).';
            
            if ($user->role_id === 1) { // Admin/Pengurus
                return redirect()->route('dashboard.index')->with('success', $successMessage);
            } 
            
            if ($user->role_id === 2) { // Anggota 
                if ($user->member) {
                    return redirect()->route('anggota.index', $user->member->id)->with('success', $successMessage);
                }
                return redirect()->route('login')->with('warning', 'Login berhasil, tetapi data profil Anggota tidak ditemukan.');
            } 
            
            if ($user->role_id === 3) { // Contoh Peran Absensi/Lainnya
                return redirect()->route('pembina.index')->with('success', $successMessage);
            }
            
            return redirect()->route('login');
        } 
        
        // 3. Jika autentikasi gagal
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->with('error', 'Login Gagal! Username atau password salah.')->onlyInput('username');
    }

    /**
     * Fungsi untuk proses logout (POST /logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login'); 
    }
}