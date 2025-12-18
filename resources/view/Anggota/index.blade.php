@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Collection;

    // Konfigurasi Carbon
    Carbon::setLocale('id');

    // --- Pengamanan Variabel ---
    $totalHadir = $totalHadir ?? 0;
    $totalIzin = $totalIzin ?? 0;
    $totalSakit = $totalSakit ?? 0;
    $totalAbsen = $totalAbsen ?? 0;
    $totalPaidCash = $totalPaidCash ?? 0;
    
    $recentAttendances = collect($recentAttendances ?? []);
    $cashStatus = collect($cashStatus ?? []); 
    $activityAttendance = $activityAttendance ?? []; 
    
    $member = $member ?? null; 
    $user = $user ?? Auth::user(); 
    
    $totalLunas = $cashStatus->where('status','lunas')->count();
    $totalBelum = $cashStatus->where('status','belum')->count();
    
    $currentMonthNum = date('n');
    $currentMonthStatus = $cashStatus->firstWhere('month_num', $currentMonthNum);
    $isPaid = $currentMonthStatus && ($currentMonthStatus['status'] ?? null) === 'lunas';

    // --- VARIABEL UNTUK PEMILIHAN BULAN ---
    $selectedMonth = $selectedMonth ?? date('n'); 
    $selectedMonthName = $selectedMonthName ?? Carbon::now()->translatedFormat('F'); 
    
    $yearMonths = collect(range(1, 12))->map(function($m) {
        return ['num' => $m, 'name' => Carbon::create(null, $m, 1)->translatedFormat('F')];
    });
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    {{-- Link Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700; --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb; --success:#16a34a; --warning:#facc15; --danger:#ef4444; --info:#3b82f6; --radius:16px; --shadow:0 14px 30px rgba(0,0,0,.08); --font:"Plus Jakarta Sans", system-ui, sans-serif;
        }
        *{box-sizing:border-box}
        body{
            margin:0; font-family:var(--font); min-height: 100vh; display: flex;
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%), radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg);
            color:var(--text);
        }
        
        /* --- SIDEBAR --- */
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); flex-shrink: 0; }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: #fff; background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, var(--accent), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding:10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); width: 20px; text-align: center; }
        .sidebar-menu > li > a { display:flex; align-items:center; padding:12px 16px; text-decoration:none; font-size:15px; color:#0f172a; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu a:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu a.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-menu a.active i { color: var(--primary); }

        /* --- CONTENT --- */
        .content-wrapper { flex:1; display:flex; flex-direction:column; min-width:0; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; font-size: 1.05em; }

        .main-content { padding: 30px 26px; flex-grow:1; }
        .panel{ background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); padding: 20px; margin-bottom: 20px; }
        
        .alert-total{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-radius:14px; background:rgba(59,130,246,.10); border:1px solid rgba(59,130,246,.22); color:#1e40af; font-weight:900; }
        .badge{ display:inline-flex; align-items:center; padding:5px 12px; border-radius:999px; font-weight:900; font-size:.8em; border:1px solid rgba(0,0,0,.06); }
        .badge.success{ background: #dcfce7; color: #166534; }
        .badge.danger{ background: #fee2e2; color: #991b1b; }

        .chart-container { height: 250px; width: 100%; position: relative; }
        .footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); }

        @media (max-width: 900px){ .sidebar { display: none; } .main-content { padding: 18px; } }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="sb-title">UKM</div>
        <div class="sb-subtitle">PAGARNUSA</div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="{{ route('anggota.index') }}" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="{{ route('anggota.kehadiran') }}"><i class="fas fa-calendar-check"></i> Status Kehadiran</a></li>
        <li><a href="{{ route('anggota.kas') }}"><i class="fas fa-wallet"></i> Status Kas</a></li>
        <li style="margin-top: 20px; border-top: 1px solid #eee;">
            <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="color: var(--danger);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content-wrapper">
    <div class="header-top">
        <h1>Dashboard Anggota</h1>
        <div class="profile-access">
            <span>{{ $member->name ?? $user->name ?? 'Anggota' }}</span>
            <span class="profile-icon-circle"><i class="fas fa-user"></i></span>
        </div>
    </div>

    <div class="main-content">
        {{-- NOTIFIKASI --}}
        <div class="panel" style="background: rgba(59,130,246,0.05); border-left: 5px solid var(--info);">
            <div class="alert-total" style="background:transparent; border:none; padding:0;">
                <div><i class="fas fa-bell"></i> Notifikasi Terbaru</div>
                <strong>Informasi UKM</strong>
            </div>
            <p style="margin:10px 0 0; color: var(--muted); font-weight: 700; font-size: 0.9em;">
                Selamat datang di sistem informasi UKM Pagar Nusa. Pantau grafik kehadiran dan iuran Anda secara berkala.
            </p>
        </div>

        {{-- RINGKASAN GRAFIK --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-bottom:20px;">
            <div class="panel">
                <h3 style="margin:0 0 15px 0; color:var(--primary); font-weight:900;"><i class="fas fa-chart-pie"></i> Kehadiran Total</h3>
                <div class="chart-container">
                    <canvas id="kehadiranChart"></canvas>
                </div>
                <p style="margin-top:15px; font-weight:800; font-size:0.9em; color:var(--muted); text-align:center;">
                    Total Sesi: {{ $totalHadir + $totalIzin + $totalSakit + $totalAbsen }} Kegiatan
                </p>
            </div>

            <div class="panel">
                <h3 style="margin:0 0 15px 0; color:var(--primary); font-weight:900;"><i class="fas fa-circle-notch"></i> Status Kas {{ date('Y') }}</h3>
                <div class="chart-container">
                    <canvas id="kasChart"></canvas>
                </div>
                <div style="margin-top:15px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:700; font-size:0.85em; color:var(--muted);">Bulan Ini ({{ Carbon::now()->translatedFormat('F') }}):</span>
                    <span class="badge {{ $isPaid ? 'success' : 'danger' }}">{{ $isPaid ? 'Lunas' : 'Belum Bayar' }}</span>
                </div>
            </div>
        </div>

        {{-- GRAFIK BATANG BULANAN --}}
        <div class="panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h3 style="margin:0; color:var(--primary); font-weight:900;"><i class="fas fa-chart-bar"></i> Statistik Kehadiran Bulanan</h3>
                <select id="monthSelector" style="padding: 8px 15px; border-radius: 10px; border: 1px solid var(--border); font-weight: 800; color: var(--primary); outline:none; cursor:pointer;">
                    @foreach($yearMonths as $month)
                        <option value="{{ $month['num'] }}" {{ $selectedMonth == $month['num'] ? 'selected' : '' }}>
                            {{ $month['name'] }} {{ date('Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(empty($activityAttendance))
                <div style="text-align:center; padding: 60px 20px; color: var(--muted);">
                    <i class="fas fa-calendar-times fa-3x" style="opacity: 0.2; margin-bottom: 10px;"></i>
                    <p style="font-weight: 700;">Tidak ada data kehadiran untuk bulan {{ $selectedMonthName }}.</p>
                </div>
            @else
                <div class="chart-container" style="height: 300px;">
                    <canvas id="activityAttendanceChart"></canvas> 
                </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <span>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA</span>
        <span><i class="fas fa-shield-alt"></i> Wira Laga Santri</span>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = { present: '#16a34a', permission: '#facc15', sick_leave: '#3b82f6', absent: '#ef4444' };
    
    // Month Selector Redirect
    const monthSelector = document.getElementById('monthSelector');
    if (monthSelector) {
        monthSelector.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('month', this.value);
            window.location.href = url.href;
        });
    }

    // 1. KEHADIRAN TOTAL (PIE)
    new Chart(document.getElementById('kehadiranChart'), {
        type: 'pie',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Absen'],
            datasets: [{
                data: [{{ $totalHadir }}, {{ $totalIzin }}, {{ $totalSakit }}, {{ $totalAbsen }}],
                backgroundColor: [colors.present, colors.permission, colors.sick_leave, colors.absent],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    // 2. KAS (DOUGHNUT)
    new Chart(document.getElementById('kasChart'), {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum'],
            datasets: [{
                data: [{{ $totalLunas }}, {{ $totalBelum }}],
                backgroundColor: [colors.present, colors.absent],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
    });

    // 3. BAR CHART BULANAN
    const activityData = @json($activityAttendance); 
    if (activityData && activityData.length > 0) {
        new Chart(document.getElementById('activityAttendanceChart'), {
            type: 'bar',
            data: {
                labels: activityData.map(item => item.label),
                datasets: [
                    { label: 'Hadir', data: activityData.map(item => item.Hadir), backgroundColor: colors.present },
                    { label: 'Izin', data: activityData.map(item => item.Izin), backgroundColor: colors.permission },
                    { label: 'Sakit', data: activityData.map(item => item.Sakit), backgroundColor: colors.sick_leave },
                    { label: 'Absen', data: activityData.map(item => item.Absen), backgroundColor: colors.absent }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { position: 'top' } }
            }
        });
    }
});
</script>
</body>
</html>