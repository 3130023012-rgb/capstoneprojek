<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Trainer;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Menampilkan dashboard Kehadiran (Grafik & Tabel).
     */
    public function absensi(Request $request) // <-- MODIFIKASI: Menerima Request
    {
        // Ambil nilai filter bulan dari request, defaultnya adalah bulan saat ini (Y-m)
        $filterMonth = $request->input('month_filter', Carbon::now()->format('Y-m'));
        
        try {
            // Parsing filter menjadi objek Carbon
            $filterDate = Carbon::createFromFormat('Y-m', $filterMonth);
        } catch (\Exception $e) {
            // Jika format invalid, gunakan bulan saat ini
            $filterDate = Carbon::now();
        }
        
        $targetMonthStart = $filterDate->copy()->startOfMonth();
        $targetMonthEnd = $filterDate->copy()->endOfMonth();
        
        // 1. Ambil data untuk GRAFIK (Persentase Bulanan)
        $attendanceStats = Attendance::join('activities', 'attendances.activity_id', '=', 'activities.id')
            ->whereBetween('activities.date', [$targetMonthStart, $targetMonthEnd]) // <-- FILTER DITERAPKAN DI SINI
            ->select(DB::raw('count(*) as total'), 'status')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $totalRecords = array_sum($attendanceStats);

        $monthlyAttendanceData = [
            'present' => $totalRecords > 0 ? round(($attendanceStats['present'] ?? 0) / $totalRecords * 100) : 0,
            'absent' => $totalRecords > 0 ? round(($attendanceStats['absent'] ?? 0) / $totalRecords * 100) : 0,
            'sick_leave' => $totalRecords > 0 ? round(($attendanceStats['sick_leave'] ?? 0) / $totalRecords * 100) : 0,
            'permission' => $totalRecords > 0 ? round(($attendanceStats['permission'] ?? 0) / $totalRecords * 100) : 0,
        ];


        // 2. Ambil data untuk TABEL RINGKASAN (Status Kehadiran per Kegiatan)
        // Catatan: Query ini TIDAK difilter berdasarkan bulan agar selalu menampilkan kegiatan terbaru.
        $attendanceStatus = Activity::with('trainer', 'attendances')
            ->orderBy('date', 'desc')
            ->take(10) 
            ->get()
            ->map(function ($activity) {
                // Hitung status
                $totalAttendees = $activity->attendances->count();
                $presentCount = $activity->attendances->where('status', 'present')->count();
                $percentage = $totalAttendees > 0 ? round(($presentCount / $totalAttendees) * 100) : 0;
                
                return [
                    'trainer_name' => $activity->trainer->name ?? 'N/A',
                    'date' => $activity->date,
                    'material' => $activity->material,
                    'attendance_percentage' => $percentage,
                ];
            });

        // 3. Ambil data untuk TABEL RINCIAN PER ANGGOTA ($detailedAttendance)
        // Catatan: Query ini TIDAK difilter berdasarkan bulan agar selalu menampilkan rincian terbaru.
        $detailedAttendance = Attendance::with(['member', 'activity.trainer'])
            ->join('activities', 'attendances.activity_id', '=', 'activities.id')
            ->orderBy('activities.date', 'desc')
            ->select('attendances.*')
            ->take(20)
            ->get();


        // MENGIRIM SEMUA DATA KE VIEW
        return view('absensi', [
            'monthlyAttendanceData' => $monthlyAttendanceData,
            'attendanceStatus' => $attendanceStatus,
            'detailedAttendance' => $detailedAttendance,
        ]);
    }

    /**
     * Menampilkan formulir untuk menambah data kehadiran baru.
     */
    public function create()
    {
        $members = Member::all(); 
        $trainers = collect(); 
        return view('create', compact('trainers', 'members'));
    }

    /**
     * Menyimpan data kehadiran yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trainer_name' => 'required|string|max:255', 
            'date' => 'required|date',
            'material' => 'required|string|max:255',
            'member_statuses' => 'required|array',
            'member_statuses.*' => 'required|in:present,absent,sick_leave,permission',
        ]);
        
        $trainer = Trainer::firstOrCreate(
            ['name' => $validated['trainer_name']]
        );

        $activity = Activity::create([
            'trainer_id' => $trainer->id,
            'date' => $validated['date'],
            'material' => $validated['material'],
            'total_members' => count($validated['member_statuses']),
        ]);

        foreach ($validated['member_statuses'] as $memberId => $status) {
            Attendance::create([
                'member_id' => $memberId,
                'activity_id' => $activity->id,
                'status' => $status,
            ]);
        }

        return redirect()->route('absensi')->with('success', 'Data kehadiran kegiatan baru berhasil ditambahkan!');
    }
}