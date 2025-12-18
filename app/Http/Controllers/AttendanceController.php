<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman utama Absensi/Kehadiran dengan filter dan data tabel.
     * Route: absensi.index
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Filter Waktu
        $currentDate = Carbon::now();
        $monthFilter = $request->input('month_filter', $currentDate->format('Y-m'));
        $dateFilter = $request->input('date_filter');
        $search = $request->input('search');

        // Tentukan periode filter
        if ($dateFilter) {
            $startDate = Carbon::parse($dateFilter)->startOfDay();
            $endDate = Carbon::parse($dateFilter)->endOfDay();
            $titleDate = $startDate;
            $currentFilter = $dateFilter; // Untuk dikirim kembali ke view
        } else {
            $startDate = Carbon::parse($monthFilter)->startOfMonth();
            $endDate = Carbon::parse($monthFilter)->endOfMonth();
            $titleDate = $startDate;
            $currentFilter = $monthFilter; // Untuk dikirim kembali ke view
        }

        // 2. Penghitungan Data Absensi Bulanan (untuk Chart)
        $monthlyAttendanceQuery = Attendance::whereHas('activity', function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        });
        
        $totalMonthlyAttendance = $monthlyAttendanceQuery->count();
        $statusCounts = $monthlyAttendanceQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $monthlyAttendanceData = [
            'present' => 0, 'absent' => 0, 'sick_leave' => 0, 'permission' => 0,
        ];

        if ($totalMonthlyAttendance > 0) {
            foreach ($statusCounts as $status => $count) {
                if (isset($monthlyAttendanceData[$status])) {
                    $percentage = round(($count / $totalMonthlyAttendance) * 100);
                    $monthlyAttendanceData[$status] = $percentage;
                }
            }
        }
        
        // 3. Rincian Kehadiran Anggota (untuk Tabel)
        $detailedAttendance = Attendance::with(['member', 'activity.trainer'])
            ->whereHas('activity', function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('member_id', 'like', '%' . $search . '%')
                      ->orWhere('study_program', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // 4. Kirimkan data ke view absensi.index
        return view('absensi.absensi', [
            'monthlyAttendanceData' => $monthlyAttendanceData,
            'detailedAttendance' => $detailedAttendance,
            'titleDate' => $titleDate,
            'currentFilter' => $currentFilter,
            'selectedDate' => $dateFilter,
        ]);
    }

    /**
     * Menampilkan formulir untuk mencatat absensi baru.
     * Route: absensi.create
     */
    public function create()
    {
        $members = Member::orderBy('name')->get();
        return view('absensi.create', compact('members')); 
    }

    /**
     * Menyimpan Kegiatan baru dan mencatat absensi.
     * Route: absensi.store
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'material' => 'required|string|max:255',
            'trainer_name' => 'required|string|max:255',
            'member_statuses' => 'required|array',
            'member_statuses.*' => ['required', Rule::in(['present', 'absent', 'sick_leave', 'permission'])],
        ]);

        DB::beginTransaction();
        try {
            $trainer = Trainer::firstOrCreate(
                ['name' => $validated['trainer_name']],
                ['phone_number' => null]
            );

            $activity = Activity::create([
                'date' => $validated['date'],
                'material' => $validated['material'],
                'trainer_id' => $trainer->id,
            ]);

            $activityId = $activity->id;
            $memberStatuses = $validated['member_statuses'];
            
            foreach ($memberStatuses as $memberId => $status) {
                Attendance::updateOrCreate(
                    ['activity_id' => $activityId, 'member_id' => $memberId],
                    ['status' => $status]
                );
            }
            
            DB::commit();
            return redirect()->route('absensi.index')->with('success', 'Kegiatan dan Absensi berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat absensi: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan halaman pemilihan kegiatan untuk update absensi.
     * Route: absensi.select
     */
    public function selectActivity()
    {
        // Ambil data Kegiatan yang tanggalnya sudah lewat atau hari ini
        $activities = Activity::with('trainer') 
                            ->where('date', '<=', Carbon::now()->endOfDay())
                            ->orderBy('date', 'desc')
                            ->get();

        return view('absensi.select_activity', compact('activities')); 
    }

    /**
     * Menampilkan formulir edit absensi untuk Kegiatan tertentu.
     * Route: absensi.edit
     */
    public function editAttendance(Activity $activity)
    {
        // 1. Ambil SEMUA anggota terdaftar (Sumber data utama)
        $members = Member::orderBy('name')->get();
        
        // --- DEBUG: Pastikan anggota ada ---
        if ($members->isEmpty()) {
            return redirect()->route('absensi.select')->with('info', 'Tidak ada data Anggota (Member) yang terdaftar. Harap tambahkan anggota terlebih dahulu.');
        }

        // 2. Ambil status kehadiran lama untuk kegiatan yang dipilih
        // Array: [member_id => status]
        $memberStatuses = Attendance::where('activity_id', $activity->id)
                                    ->pluck('status', 'member_id')
                                    ->toArray();
        
        // 3. Kirim ke View. 
        return view('absensi.edit_attendance', [
            'activity' => $activity,
            'members' => $members, 
            'memberStatuses' => $memberStatuses, 
        ]);
    }

    /**
     * Menyimpan (UPDATE) perubahan absensi massal.
     * Route: absensi.update_bulk
     */
    public function updateBulkAttendance(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => ['required', Rule::in(['present', 'absent', 'sick_leave', 'permission'])],
        ]);

        DB::beginTransaction();
        try {
            $memberStatuses = $validated['attendance'];
            
            $updatedCount = 0;
            foreach ($memberStatuses as $memberId => $status) {
                $attendance = Attendance::updateOrCreate(
                    ['activity_id' => $activity->id, 'member_id' => $memberId],
                    ['status' => $status]
                );
                
                if ($attendance->wasRecentlyCreated || $attendance->wasChanged()) {
                    $updatedCount++;
                }
            }

            DB::commit();
            return redirect()->route('absensi.index')->with('success', "Absensi kegiatan '{$activity->material}' berhasil diperbarui! ($updatedCount data diubah/ditambahkan)");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui absensi: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan laporan absensi (digunakan oleh route laporan.absensi).
     * Route: laporan.absensi
     */
    public function report(Request $request)
{
    // Ambil filter bulan dari request
    $filterDate = $request->input('filter_month', \Carbon\Carbon::now()->format('Y-m'));
    $titleDate = \Carbon\Carbon::createFromFormat('Y-m', $filterDate);
    
    $startDate = $titleDate->copy()->startOfMonth()->toDateString();
    $endDate = $titleDate->copy()->endOfMonth()->toDateString();

    // Pastikan data ini diambil agar tidak kosong di tabel
    $members = \App\Models\Member::orderBy('name', 'asc')->get();
    $activities = \App\Models\Activity::whereBetween('date', [$startDate, $endDate])
                                      ->orderBy('date', 'asc')
                                      ->get();
    $attendances = \App\Models\Attendance::whereIn('activity_id', $activities->pluck('id'))
                                          ->get();

    return view('laporan.absensi', compact('titleDate', 'filterDate', 'members', 'activities', 'attendances'));
}
}
