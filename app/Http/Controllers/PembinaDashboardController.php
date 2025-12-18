<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; // Untuk menghitung anggota
use App\Models\Activity; // Untuk menghitung kegiatan
use App\Models\KasTransaction; // Untuk ringkasan kas
use Illuminate\Support\Facades\DB;

class PembinaDashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk peran Pembina.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Ambil data User yang sedang login
        $user = Auth::user();
        
        // 2. Metrik Utama (Mirip Admin, fokus pada pengawasan)
        $totalMembers = Member::count();
        $totalActivities = Activity::count();
        
        // Ringkasan Kas
        $totalIn = KasTransaction::where('type', 'in')->sum('amount');
        $totalOut = KasTransaction::where('type', 'out')->sum('amount');
        $totalKas = $totalIn - $totalOut;

        // 3. Data Khusus Pembina (Contoh: 5 Kegiatan Terbaru)
        $latestActivities = Activity::latest()->limit(5)->get();
        
        // Anda bisa menambahkan logika lain di sini, 
        // seperti: Laporan Kinerja Bulanan, Statistik Absensi Total, dll.

        return view('pembina.index', [
            'user' => $user,
            'totalMembers' => $totalMembers,
            'totalActivities' => $totalActivities,
            'totalKas' => $totalKas,
            'latestActivities' => $latestActivities,
        ]);
    }
}