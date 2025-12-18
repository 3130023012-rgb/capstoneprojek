<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\KasTransaction; // Ganti CashPayment menjadi KasTransaction

class AnggotaController extends Controller
{
    // Fungsi bantuan untuk mendapatkan nama bulan
    private function getMonthName($monthNumber)
    {
        return \Carbon\Carbon::create(null, $monthNumber, 1)->translatedFormat('F');
    }

    public function index()
    {
        // ASUMSI PENTING: ID pengguna yang login (Auth::id()) adalah member_id
        $memberId = Auth::id(); 
        $currentYear = date('Y');

        // --- Status Kehadiran (TIDAK BERUBAH dari revisi sebelumnya) ---
        $totalHadir = Attendance::where('member_id', $memberId)->where('status', 'hadir')->count();
        $totalIzin = Attendance::where('member_id', $memberId)->where('status', 'izin')->count();
        $totalAlfa = Attendance::where('member_id', $memberId)->where('status', 'alfa')->count();

        $recentAttendances = Attendance::where('member_id', $memberId)
                                    ->with('activity') 
                                    ->latest() 
                                    ->limit(5)
                                    ->get();

        // --- Status Kas (BARU: Menggunakan KasTransaction) ---
        
        // 1. Ambil semua transaksi Kas (Iuran) yang merupakan pemasukan ('in') dari anggota ini
        $memberCashPayments = KasTransaction::where('member_id', $memberId)
                                    ->where('type', 'in') // Hanya hitung pemasukan (iuran)
                                    ->whereYear('date', $currentYear)
                                    ->get();
        
        // 2. Hitung total yang sudah dibayar
        $totalPaidCash = $memberCashPayments->sum('amount');
        
        // 3. Siapkan array status kas per bulan
        $cashStatus = [];
        $months = range(1, 12); // Bulan 1 sampai 12
        
        foreach ($months as $month) {
            $payment = $memberCashPayments->first(function ($transaction) use ($month) {
                // Mengecek bulan dari kolom 'date'
                return $transaction->date->month == $month; 
            });

            $cashStatus[] = [
                'month_num' => $month,
                'month_name' => $this->getMonthName($month),
                'status' => $payment ? 'lunas' : 'belum',
                'amount' => $payment ? $payment->amount : 0,
            ];
        }

        return view('anggota.index', compact(
            'recentAttendances', 
            'totalHadir', 
            'totalIzin', 
            'totalAlfa', 
            'cashStatus', // Mengirim array yang sudah diproses
            'totalPaidCash'
        ));
    }
}