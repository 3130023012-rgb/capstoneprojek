<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Activity;
use App\Models\KasTransaction; 
use App\Models\Attendance; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // =================================================================
        // 1. SETUP FILTER WAKTU
        // =================================================================
        $filterYear = $request->input('year', Carbon::now()->year);
        $filterMonth = $request->input('month', Carbon::now()->month);
        
        try {
            // Tentukan rentang waktu untuk filter Absensi
            $currentMonthStart = Carbon::createFromDate($filterYear, $filterMonth, 1)->startOfMonth();
            $currentMonthEnd = $currentMonthStart->copy()->endOfMonth();
        } catch (\Exception $e) {
            // Fallback jika input filter tidak valid
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
            $filterYear = Carbon::now()->year;
            $filterMonth = Carbon::now()->month;
        }

        // =================================================================
        // 2. METRIK RINGKASAN & TOTAL KAS
        // =================================================================
        $totalMembers = Member::count();
        $totalActivities = Activity::count();
        
        $totalIn = KasTransaction::where('type', 'in')->sum('amount');
        $totalOut = KasTransaction::where('type', 'out')->sum('amount');
        $totalKas = $totalIn - $totalOut;

        // =================================================================
        // 3. DATA GRAFIK KAS BULANAN (Bar Chart)
        // =================================================================
        // Query untuk Kas (tetap menggunakan kolom 'date' di KasTransaction)
        $monthlyData = KasTransaction::select(
                DB::raw("DATE_FORMAT(date, '%b') as month"), 
                DB::raw("SUM(CASE WHEN type = 'in' THEN amount ELSE 0 END) as pemasukan"),
                DB::raw("SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) as pengeluaran")
            )
            ->where('date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(date, '%b')"))
            ->orderBy('date', 'asc')
            ->get();
            
        $monthlyKas = [
            'labels' => $monthlyData->pluck('month')->toArray(),
            'pemasukan' => $monthlyData->pluck('pemasukan')->toArray(),
            'pengeluaran' => $monthlyData->pluck('pengeluaran')->toArray(),
        ];

        // =================================================================
        // 4. DATA GRAFIK ABSENSI (Pie Chart) - MENGGUNAKAN TANGGAL KEGIATAN
        // =================================================================
        
        $startDate = $currentMonthStart;
        $endDate = $currentMonthEnd;
        
        // MENGUBAH QUERY: Memfilter Absensi melalui Tanggal di Activity
        $attendanceCounts = Attendance::whereHas('activity', function($query) use ($startDate, $endDate) {
                                         // Filter Activity berdasarkan kolom 'date'
                                         $query->whereBetween('date', [$startDate, $endDate]); 
                                     })
                                     ->select('status', DB::raw('count(*) as total'))
                                     ->groupBy('status')
                                     ->pluck('total', 'status')
                                     ->toArray();
        
        // Data Absensi yang siap untuk Pie Chart
        $monthlyAttendanceData = [
            'Hadir' => (int)($attendanceCounts['Hadir'] ?? $attendanceCounts['present'] ?? 0),
            'Absen' => (int)($attendanceCounts['Absen'] ?? $attendanceCounts['absent'] ?? 0),
            'Sakit' => (int)($attendanceCounts['Sakit'] ?? $attendanceCounts['sick_leave'] ?? 0),
            'Izin' => (int)($attendanceCounts['Izin'] ?? $attendanceCounts['permission'] ?? 0),
        ];
        
        // =================================================================
        // 5. KIRIM DATA KE VIEW
        // =================================================================
        return view('dashboard', [
            'totalMembers' => $totalMembers,
            'totalActivities' => $totalActivities,
            'totalKas' => $totalKas,
            'monthlyKas' => $monthlyKas,
            'monthlyAttendanceData' => $monthlyAttendanceData,
            'filterYear' => $filterYear,
            'filterMonth' => $filterMonth,
        ]);
    }
}