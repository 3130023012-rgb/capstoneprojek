<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\GeneralKasController;
use App\Http\Controllers\AnggotaDashboardController;
use App\Http\Controllers\PembinaDashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\Trainer\ReportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ================= LANDING PAGE =================
Route::get('/landing', [LandingPageController::class, 'index'])->name('landing');

// ================= AUTH =================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// =======================================================
// ROUTE DENGAN MIDDLEWARE AUTH (ADMIN / PENGURUS / ANGGOTA)
// =======================================================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD ADMIN (role_id 1)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['role:1'])
        ->name('dashboard.index'); 

    // Rute Anggota (role_id 2)
    Route::get('/anggota/dashboard', [AnggotaDashboardController::class, 'index'])
        ->middleware(['role:2'])
        ->name('anggota.index'); 
    Route::get('/anggota/kehadiran', [AnggotaDashboardController::class, 'kehadiran'])
        ->name('anggota.kehadiran');
    Route::get('/anggota/kas', [AnggotaDashboardController::class, 'kas'])
        ->name('anggota.kas');

    // Rute Pembina - Pindah di bawah untuk konsistensi grouping

    // ===================================================
    // MASTER DATA PENGGUNA & KONTEN (Biasanya role_id 1)
    // ===================================================
    Route::resource('member', MemberController::class);
    Route::get('/member/profile/{member}', [MemberController::class, 'profile'])
        ->name('member.profile');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/member/user/create', [MemberController::class, 'createUserForm'])
        ->name('member.user.create');

    Route::post('/member/user/store', [MemberController::class, 'storeUser'])
        ->name('member.user.store');

    Route::resource('konten', KontenController::class)->except(['show']);

    Route::get('/konten/upload', [KontenController::class, 'upload'])
        ->name('konten.upload');

    Route::post('/konten/upload', [KontenController::class, 'upload_store'])
        ->name('konten.upload.store');

    Route::delete('/konten/photo/{photo}', [KontenController::class, 'delete_photo'])
        ->name('konten.delete.photo');

    Route::get('/konten/edit-text/{key}', [KontenController::class, 'edit_text'])
        ->name('konten.edit_text');

    Route::put('/konten/edit-text/{key}', [KontenController::class, 'update_text'])
        ->name('konten.update_text');

    // ===================================================
    // ABSENSI
    // ===================================================
    Route::get('/absensi', [AttendanceController::class, 'index'])
        ->name('absensi.index');

    Route::get('/absensi/create', [AttendanceController::class, 'create'])
        ->name('absensi.create');

    Route::post('/absensi/store', [AttendanceController::class, 'store'])
        ->name('absensi.store');

    Route::get('/absensi/select', [AttendanceController::class, 'selectActivity'])
        ->name('absensi.select');

    Route::get('/absensi/{activity}/edit', [AttendanceController::class, 'editAttendance'])
        ->name('absensi.edit');

    Route::put('/absensi/{activity}/update', [AttendanceController::class, 'updateBulkAttendance'])
        ->name('absensi.update_bulk');

    // ===================================================
    // KAS ANGGOTA (ADMIN)
    // ===================================================
    Route::prefix('kas')->name('kas.')->group(function () {

        Route::get('/', [KasController::class, 'index'])->name('index');
        Route::get('/create', [KasController::class, 'create'])->name('create');
        Route::post('/store', [KasController::class, 'store'])->name('store');
        Route::get('/{kas}/edit', [KasController::class, 'edit'])->name('edit');
        Route::put('/{kas}', [KasController::class, 'update'])->name('update');
        Route::delete('/{kas}', [KasController::class, 'destroy'])->name('destroy');
    });

    // ===================================================
    // KEGIATAN
    // ===================================================
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {

        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::get('/create', [GeneralKasController::class, 'create'])->name('create');
        Route::post('/store', [GeneralKasController::class, 'store'])->name('kas.store');

        Route::get('/transaksi/{kas}/edit', [GeneralKasController::class, 'edit'])
            ->name('transaksi.edit');

        Route::put('/transaksi/{kas}', [GeneralKasController::class, 'update'])
            ->name('transaksi.update');

        Route::delete('/transaksi/{kas}', [GeneralKasController::class, 'destroy'])
            ->name('transaksi.destroy');

        Route::get('/{activity}/edit', [ActivityController::class, 'edit'])
            ->name('edit');

        Route::put('/{activity}', [ActivityController::class, 'update'])
            ->name('update');

        Route::delete('/{activity}', [ActivityController::class, 'destroy'])
            ->name('destroy');

        Route::post('/{activity}/konfirmasi', [ActivityController::class, 'updateKonfirmasi'])
            ->name('konfirmasi');
    });

    // ===================================================
    // LAPORAN ADMIN/PENGURUS (role_id 1)
    // ===================================================
    Route::get('/laporan/absensi', [AttendanceController::class, 'report'])
        ->name('laporan.absensi');

    Route::get('/laporan/kas', [KasController::class, 'report'])
        ->name('laporan.kas');

    Route::get('/laporan/kegiatan', [ActivityController::class, 'report'])
        ->name('laporan.kegiatan');

});


// =======================================================
// ROUTE KHUSUS PEMBINA (role_id 3)
// =======================================================
// Menggunakan role:3 agar konsisten dengan definisi di atas
Route::middleware(['auth', 'role:3'])->prefix('pembina')->name('pembina.')->group(function () {
    
    // RUTE DASHBOARD PEMBINA (DIPINDAHKAN KE SINI)
    Route::get('/dashboard', [PembinaDashboardController::class, 'index'])
        ->name('index');

    // START: GRUP LAPORAN
    Route::prefix('reports')->name('reports.')->group(function () {
        
        // URL: /pembina/reports/cash | NAMA RUTE: 'pembina.reports.cash'
        Route::get('/cash', [ReportController::class, 'cashReport'])->name('cash'); 
        
        // URL: /pembina/reports/attendance | NAMA RUTE: 'pembina.reports.attendance'
        Route::get('/attendance', [ReportController::class, 'attendanceReport'])->name('attendance'); 
        
        // URL: /pembina/reports/activity | NAMA RUTE: 'pembina.reports.activity'
        Route::get('/activity', [ReportController::class, 'activityReport'])->name('activity');
    });
    // END: GRUP LAPORAN
});
