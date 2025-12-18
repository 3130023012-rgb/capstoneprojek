<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; 
use App\Models\Attendance;
use App\Models\KasTransaction;
use Carbon\Carbon;

class AnggotaDashboardController extends Controller
{
    private function getMonthName($monthNumber)
    {
        // Menggunakan tahun saat ini untuk mendapatkan nama bulan dalam locale yang dikonfigurasi
        return Carbon::create(null, $monthNumber, 1)->translatedFormat('F');
    }

    // ===================================================
    // 1. DASHBOARD UTAMA (RINGKASAN)
    // ===================================================
    public function index(Request $request) // Menerima objek Request
    {
        $user = Auth::user();
        $memberData = $user->member; 
        $currentYear = date('Y');
        
        // --- LOGIKA BULAN YANG DIPILIH ---
        // 1. Ambil bulan dari request 'month', defaultnya bulan saat ini (numeric)
        $selectedMonth = $request->input('month', date('n')); 
        
        // 2. Tentukan batas waktu untuk filter kegiatan (awal & akhir bulan yang dipilih)
        try {
            // Membuat objek Carbon berdasarkan tahun saat ini dan bulan yang dipilih
            $startOfMonth = Carbon::createFromDate($currentYear, $selectedMonth, 1)->startOfMonth(); 
            $endOfMonth = Carbon::createFromDate($currentYear, $selectedMonth, 1)->endOfMonth();
            $selectedMonthName = $this->getMonthName($selectedMonth);
        } catch (\Exception $e) {
            // Fallback jika input bulan tidak valid
            $selectedMonth = date('n');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $selectedMonthName = $this->getMonthName($selectedMonth);
        }
        // --- END LOGIKA BULAN YANG DIPILIH ---

        if (!$memberData) {
             return view('anggota.index', [
                'user' => $user, 'member' => null, 
                'totalHadir' => 0, 'totalIzin' => 0, 'totalSakit' => 0, 'totalAbsen' => 0,
                'recentAttendances' => collect(), 'cashStatus' => collect(), 'activityAttendance' => [], 
                'totalPaidCash' => 0, 'totalLunas' => 0, 'totalBelum' => 0,
                'selectedMonth' => $selectedMonth, 
                'selectedMonthName' => $selectedMonthName, 
            ]);
        }
        
        $memberId = $memberData->id;

        // A. LOGIKA KEHADIRAN TOTAL (Menghitung semua riwayat)
        $totalHadir = Attendance::where('member_id', $memberId)->where('status', 'present')->count();
        $totalIzin = Attendance::where('member_id', $memberId)->where('status', 'permission')->count();
        $totalSakit = Attendance::where('member_id', $memberId)->where('status', 'sick_leave')->count();
        $totalAbsen = Attendance::where('member_id', $memberId)->where('status', 'absent')->count();

        // Ambil SEMUA data kehadiran HANYA untuk BULAN YANG DIPILIH
        $allAttendancesForChart = Attendance::where('member_id', $memberId)
                                         ->whereHas('activity', function ($query) use ($startOfMonth, $endOfMonth) {
                                             $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
                                         })
                                         ->with('activity')
                                         ->get();

        // Data kehadiran terbaru (5 data)
        $recentAttendances = Attendance::where('member_id', $memberId)
                             ->with('activity')
                             ->latest()
                             ->take(5)
                             ->get();
        

        // B. LOGIKA KAS (Status Lunas/Belum setahun penuh)
        $memberCashPayments = KasTransaction::where('member_id', $memberId)->where('type', 'in')->whereYear('date', $currentYear)->get();
        $totalPaidCash = $memberCashPayments->sum('amount');
        $cashStatus = collect(); $months = range(1, 12); $totalLunas = 0; $totalBelum = 0;
        
        foreach ($months as $month) {
            $payment = $memberCashPayments->first(fn ($transaction) => optional($transaction->date)->month == $month);
            $status = $payment ? 'lunas' : 'belum';
            if ($status === 'lunas') { $totalLunas++; } else { $totalBelum++; }
            $cashStatus->push(['month_num' => $month, 'month_name' => $this->getMonthName($month), 'status' => $status, 'amount' => $payment ? $payment->amount : 0,]);
        }
        
        // C. LOGIKA GRAFIK BATANG BERDASARKAN BULAN YANG DIPILIH
        
        $monthlyAttendanceData = [];
        $monthNum = $selectedMonth;
        $monthName = $selectedMonthName;

        // Hitung status kehadiran untuk bulan yang dipilih dari data yang sudah difilter
        $attendancesInMonth = $allAttendancesForChart->filter(function($attendance) use ($monthNum) {
            // Karena $allAttendancesForChart sudah difilter tanggalnya, kita hanya perlu menghitung statusnya.
            return optional(optional($attendance->activity)->date)->month == $monthNum;
        });
        
        $monthlyAttendanceData[] = [
            'label' => $monthName,
            'Hadir' => $attendancesInMonth->where('status', 'present')->count(),
            'Izin' => $attendancesInMonth->where('status', 'permission')->count(),
            'Sakit' => $attendancesInMonth->where('status', 'sick_leave')->count(),
            'Absen' => $attendancesInMonth->where('status', 'absent')->count(),
        ];
        
        // D. MENGIRIM DATA KE VIEW
        return view('anggota.index', [
            'user' => $user, 
            'member' => $memberData,
            'totalHadir' => $totalHadir, 
            'totalIzin' => $totalIzin, 
            'totalSakit' => $totalSakit, 
            'totalAbsen' => $totalAbsen,
            'recentAttendances' => $recentAttendances,
            'activityAttendance' => $monthlyAttendanceData,
            'cashStatus' => $cashStatus, 
            'totalPaidCash' => $totalPaidCash, 
            'totalLunas' => $totalLunas, 
            'totalBelum' => $totalBelum,
            'selectedMonth' => $selectedMonth, 
            'selectedMonthName' => $selectedMonthName, 
        ]);
    }
    
    // ===================================================
    // 2. DETAIL KEHADIRAN & 3. DETAIL KAS (Tidak Ada Perubahan)
    // ===================================================
    public function kehadiran()
    {
         $memberData = Auth::user()->member;
         if (!$memberData) {
             return redirect()->route('anggota.index')->with('error', 'Data anggota tidak ditemukan.');
         }
         $memberId = $memberData->id;
         $attendances = Attendance::where('member_id', $memberId)->with('activity')->latest()->paginate(20);
         $totalHadir = Attendance::where('member_id', $memberId)->where('status', 'present')->count();
         $totalIzin = Attendance::where('member_id', $memberId)->where('status', 'permission')->count();
         $totalSakit = Attendance::where('member_id', $memberId)->where('status', 'sick_leave')->count();
         $totalAbsen = Attendance::where('member_id', $memberId)->where('status', 'absent')->count();
         return view('anggota.kehadiran', compact('attendances', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAbsen'));
    }

    public function kas()
    {
         $memberData = Auth::user()->member;
         if (!$memberData) {
             return redirect()->route('anggota.index')->with('error', 'Data anggota tidak ditemukan.');
         }
         $memberId = $memberData->id;
         $currentYear = date('Y');
         $memberCashPayments = KasTransaction::where('member_id', $memberId)->where('type', 'in')->whereYear('date', $currentYear)->get();
         $totalPaidCash = $memberCashPayments->sum('amount');
         $cashStatus = collect(); $months = range(1, 12); 
         foreach ($months as $month) {
             $payment = $memberCashPayments->first(fn ($transaction) => optional($transaction->date)->month == $month);
             $cashStatus->push(['month_num' => $month, 'month_name' => $this->getMonthName($month), 'status' => $payment ? 'lunas' : 'belum', 'amount' => $payment ? $payment->amount : 0,]);
         }
         return view('anggota.kas', compact('cashStatus', 'totalPaidCash'));
    }
}