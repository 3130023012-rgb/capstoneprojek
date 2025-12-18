@php
    use Carbon\Carbon;
    // Asumsi semua variabel ini dikirim dari DashboardController.php:
    $totalKas = $totalKas ?? 0;
    $totalActivities = $totalActivities ?? 0;
    $totalMembers = $totalMembers ?? 0;
    $monthlyKas = $monthlyKas ?? ['labels' => [], 'pemasukan' => [], 'pengeluaran' => []];
    $monthlyAttendanceData = $monthlyAttendanceData ?? ['Hadir' => 0, 'Absen' => 0, 'Sakit' => 0, 'Izin' => 0];
    $filterYear = $filterYear ?? Carbon::now()->year;
    $filterMonth = $filterMonth ?? Carbon::now()->month;

    // LOGIKA PERHITUNGAN PRESENTASE TOTAL HADIR (Untuk ditampilkan di tengah Donut)
    $p = (int) ($monthlyAttendanceData['Hadir'] ?? 0);
    $i = (int) ($monthlyAttendanceData['Izin'] ?? 0);
    $total = $p + (int)($monthlyAttendanceData['Absen'] ?? 0) + (int)($monthlyAttendanceData['Sakit'] ?? 0) + $i;
    
    $presentPct = 0;
    if ($total > 0) {
        $presentPct = (int) round((($p + $i) / $total) * 100); 
    }
    $totalPresentText = $presentPct . '%'; // Teks yang akan ditampilkan di tengah Donut

    // Fungsi helper format Rupiah (Opsional, diasumsi sudah ada di PHP)
    if (!function_exists('formatRupiah')) {
        function formatRupiah($amount) {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin UKM PAGARNUSA - Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        /*
        |====================================
        | Kustomisasi Warna Pagar Nusa (Hijau & Emas)
        |====================================
        */
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-dark: #343a40; --color-light: #f8f9fa; --color-white: #fff; --color-success: #28a745; 
            --color-primary: #007bff; --color-danger: #dc3545; --color-warning: #ffc107; --color-success-text: #155724;
            --font: "Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        *{ box-sizing:border-box; }

        body{
            font-family: var(--font); margin: 0; min-height: 100vh; display: flex; color: var(--text);
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%),
            radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg);
        }

        /* SIDEBAR (Dipertahankan) */
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: var(--color-white); background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding: 10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); } /* Ikon di menu */
        .sidebar-menu > li > a { display:flex; align-items:center; }

        /* Menu active */
        .sidebar-menu a.active, .sidebar-menu summary.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-menu a.active i, .sidebar-menu summary.active i { color: var(--primary); }

        .sidebar-menu summary::-webkit-details-marker { display:none; }
        .sidebar-menu summary{ list-style:none; display:flex; justify-content:space-between; align-items:center; padding:12px 16px; font-size:15px; color:#0f172a; cursor:pointer; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu summary:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu summary::after{ content:'▼'; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
        .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }

        .sidebar-menu > li > a{ padding:12px 16px; text-decoration:none; font-size:15px; color:#0f172a; display:block; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu > li > a:hover{ background: rgba(0,66,37,.06); }


        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:flex; align-items:center; padding:8px 12px; margin:2px 12px 2px 0; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* Konten Utama */
        .content{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }

        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.5em; font-weight: 950; color: var(--primary); letter-spacing:.2px; } /* Ukuran judul diperbesar */

        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); transition: .15s ease; color: var(--primary); font-weight: 900; }
        .profile-access:hover{ background: rgba(255,215,0,.18); border-color: rgba(255,215,0,.35); transform: translateY(-1px); }
        .profile-name{ font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); box-shadow: 0 10px 18px rgba(0,66,37,.18); font-size: 1.05em; }

        .main-content-area{ padding: 30px 26px 20px; flex-grow: 1; min-width: 0; } /* Padding atas sedikit ditingkatkan */

        .section-title{ color: #0f172a; font-size: 1.8em; font-weight: 950; margin: 0 0 24px; text-align:left; letter-spacing:.15px; border-bottom: 2px solid var(--accent); padding-bottom: 8px; display:inline-block; } /* Judul section diperjelas */

        /* METRICS CARD REVISI */
        .metrics-grid{ 
            display:grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 20px; /* Jarak antar card diperlebar */
            margin-bottom: 30px; 
        }
        .metric-card{ 
            border: 1px solid rgba(0,66,37,.15); /* Border lebih hijau */
            padding: 20px; 
            border-radius: 20px; /* Radius sedikit lebih besar */
            background: var(--card); 
            box-shadow: 0 8px 18px rgba(0,0,0,.08); /* Shadow sedikit diubah */
            min-height: 110px; 
            position: relative; 
            overflow:hidden;
            transition: transform .2s ease;
        }
        .metric-card:hover {
            transform: translateY(-3px);
        }
        .metric-card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.2em;
            color: var(--accent);
            opacity: 0.8;
        }
        .metric-card small{ 
            font-size: 1em; 
            color: var(--muted); 
            display:block; 
            margin-bottom: 8px; 
            font-weight: 800; 
            letter-spacing: .5px;
        }
        .metric-card p{ 
            font-size: 1.8em; 
            font-weight: 950; 
            margin: 0; 
            color: var(--primary); 
            letter-spacing:.2px; 
            line-height: 1.2;
        }

        /* CHART & FILTER */
        .chart-title-sub{ font-size: 1.6em; font-weight: 950; margin: 30px 0 15px; color: var(--primary); text-align:left; }
        .chart-container-grid { 
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 26px;
        }
        .chart-card{ 
            border: 1px solid var(--border); 
            padding: 20px; 
            border-radius: var(--radius); 
            background: var(--card); 
            box-shadow: var(--shadow-md); 
        }
        .chart-legend{ 
            text-align:center; 
            font-size: .9em; 
            color: var(--muted); 
            margin-top: 12px; 
            font-weight: 800; 
            display: flex; 
            justify-content: center; 
            flex-wrap: wrap; 
            gap: 10px;
        }
        .chart-legend .legend-item {
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid;
            font-weight: 800;
            white-space: nowrap;
        }
        .filter-absensi{ display:flex; justify-content:flex-end; align-items:center; gap: 10px; margin: 0 0 15px; flex-wrap: wrap; }
        .filter-absensi form { display:flex; gap: 10px; }
        .filter-absensi select, .filter-absensi button{ padding: 10px 12px; border: 1px solid var(--border); border-radius: 14px; font-size: .95em; font-family: var(--font); font-weight: 800; background: #fff; outline: none; transition: all .15s ease; }
        .filter-absensi select:focus, .filter-absensi button:focus{ border-color: rgba(0,66,37,.45); box-shadow: 0 0 0 4px rgba(255,215,0,.22); }
        .filter-absensi button{ background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #fff; cursor: pointer; border: none; box-shadow: 0 14px 24px rgba(0,66,37,.18); }
        .filter-absensi button:hover{ transform: translateY(-1px); filter: brightness(.98); }

        /* FIX DONUT */
        .chart-square{ 
            width: min(100%, 420px); 
            aspect-ratio: 1 / 1; 
            margin: 0 auto; 
            position: relative; 
            display: flex;
            flex-direction: column;
            justify-content: center; 
            align-items: center; 
            padding: 10px; /* Menambahkan padding agar chart tidak menempel */
        }
        .chart-square #absensiPieChart{ 
            position: absolute; 
            inset: 0; 
            width: 100% !important; 
            height: 100% !important; 
            display: block; 
        }
        .empty-state-message {
            text-align: center;
            padding: 40px 20px; 
            color: var(--color-danger);
            font-weight: 900;
            z-index: 5; 
        }
        .total-percentage-center { display: none; } /* Nonaktifkan karena menggunakan plugin */

        /* FOOTER */
        .footer{ 
            background: var(--card); 
            padding: 14px 26px; 
            border-top: 1px solid var(--border); 
            text-align: center; 
            font-size: .9em; 
            color: var(--muted); 
            display:flex; 
            justify-content: space-between; /* Mengubah menjadi space-between */
            align-items: center;
            flex-wrap:wrap; 
        }
        .footer span{ font-weight: 700; }
        .footer-left { display: flex; gap: 20px; }

        /* Responsive */
        @media (max-width: 900px){ 
            .metrics-grid{ grid-template-columns: 1fr; } 
            .main-content-area{ padding: 18px; } 
            .chart-card{ padding: 16px; } 
            .chart-container-grid { grid-template-columns: 1fr; } /* Chart menjadi 1 kolom di HP */
            .sidebar { width: 100%; border-right: none; position: relative; }
            body { flex-direction: column; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            UKM PAGARNUSA UNUSA
        </div>
        <ul class="sidebar-menu">

            {{-- 1. Dashboard (Active) --}}
            <li><a href="{{ route('dashboard.index') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>

            {{-- 2. MASTER DATA PENGGUNA (Dropdown) --}}
            <li><details><summary><i class="fas fa-users-cog"></i> Master Data Pengguna</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('member.index') }}">Manajemen Anggota</a></li>
                    <li><a href="{{ route('konten.index') }}">Manajemen Konten</a></li>
                </ul>
            </details></li>

            {{-- 3. Absensi --}}
            <li><a href="{{ route('absensi.index') }}"><i class="fas fa-clipboard-check"></i> Absensi</a></li>

            {{-- 4. Transaksi (Dropdown) --}}
            <li><details><summary><i class="fas fa-cash-register"></i> Transaksi</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('kas.index') }}">Kas</a></li>
                    <li><a href="{{ route('kegiatan.index') }}">Kegiatan</a></li>
                </ul>
            </details></li>

            {{-- 5. Laporan (Dropdown) --}}
            <li><details><summary><i class="fas fa-file-alt"></i> Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}">Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}">Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li>
                </ul>
            </details></li>

            {{-- 6. Logout --}}
            <li style="margin-top: 20px; border-top: 1px solid #eee;">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--danger); font-weight: bold;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <div class="content">
        <div class="header-top">
            <h1><i class="fas fa-chart-bar" style="margin-right: 10px;"></i> Dashboard Administrasi</h1>
            <a href="{{ route('profile.edit') }}" class="profile-access">
                <span class="profile-name">Admin</span>
                <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </a>
        </div>

        <div class="main-content-area">
            
            <h2 class="section-title">Ringkasan Statistik UKM</h2>
            
            {{-- METRICS GRID --}}
            <div class="metrics-grid">
                
                {{-- Card Saldo Kas --}}
                <div class="metric-card">
                    <span class="metric-card-icon"><i class="fas fa-wallet"></i></span>
                    <small>Saldo Kas Terkini</small>
                    <p>{{ formatRupiah($totalKas ?? 0) }}</p>
                </div>

                {{-- Card Jumlah Kegiatan --}}
                <div class="metric-card">
                    <span class="metric-card-icon"><i class="fas fa-calendar-check"></i></span>
                    <small>Kegiatan Terdaftar</small>
                    <p>{{ number_format($totalActivities ?? 0) }}</p>
                </div>

                {{-- Card Total Anggota --}}
                <div class="metric-card">
                    <span class="metric-card-icon"><i class="fas fa-users"></i></span>
                    <small>Total Anggota Aktif</small>
                    <p>{{ number_format($totalMembers ?? 0) }}</p>
                </div>
            </div>

            {{-- CHART CONTAINER (2 Kolom) --}}
            <div class="chart-container-grid">
                
                {{-- KOLOM 1: BAR CHART KAS --}}
                <div>
                    <h2 class="chart-title-sub">Alur Kas Bulanan</h2>
                    <div class="chart-card">
                        <div style="height: 320px;">
                            <canvas id="kasBarChart"></canvas>
                            
                        </div>
                        <div class="chart-legend" style="margin-top: 15px;">Pemasukan (Hijau) vs Pengeluaran (Merah) 6 bulan terakhir</div>
                    </div>
                </div>

                {{-- KOLOM 2: DOUGHNUT ABSENSI --}}
                <div>
                    <h2 class="chart-title-sub">Persentase Kehadiran</h2>
                    
                    {{-- FILTER ABSENSI --}}
                    <div class="filter-absensi">
                        <form method="GET" action="{{ route('dashboard.index') }}">
                            <select name="month" id="month-filter">
                                @php
                                    $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                @endphp
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key + 1 }}" {{ ($filterMonth == $key + 1) ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="year" id="year-filter">
                                @php
                                    $currentYear = date('Y');
                                    for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                                        echo "<option value=\"$i\" " . ($filterYear == $i ? 'selected' : '') . ">$i</option>";
                                    }
                                @endphp
                            </select>

                            <button type="submit">Filter <i class="fas fa-filter"></i></button>
                        </form>
                    </div>

                    <div class="chart-card">
                        <div class="chart-square">
                            @php
                                $total = array_sum($monthlyAttendanceData);
                            @endphp

                            @if ($total === 0)
                                <p class="empty-state-message">
                                    <i class="fas fa-exclamation-triangle"></i> Tidak ada data Absensi untuk {{ Carbon::createFromDate($filterYear, $filterMonth, 1)->translatedFormat('F Y') }}
                                </p>
                            @else
                                <canvas id="absensiPieChart"></canvas>
                                
                            @endif
                        </div>
                        
                        {{-- LEGEND (di luar chart-square) --}}
                        @if ($total > 0)
                            <div class="chart-legend" style="margin-top: 20px;">
                                <span class="legend-item" style="background: rgba(0,150,60,.12); color: rgb(0, 150, 60); border-color: rgba(0,150,60,.30);">
                                    Hadir ({{ (int) round(($monthlyAttendanceData['Hadir'] / $total) * 100) }}%)
                                </span>
                                <span class="legend-item" style="background: rgba(220,53,69,.12); color:#dc3545; border-color: rgba(220,53,69,.20);">
                                    Absen ({{ (int) round(($monthlyAttendanceData['Absen'] / $total) * 100) }}%)
                                </span>
                                <span class="legend-item" style="background: rgba(255,215,0,.22); color:#cca000; border-color: rgba(255,215,0,.35);">
                                    Sakit ({{ (int) round(($monthlyAttendanceData['Sakit'] / $total) * 100) }}%)
                                </span>
                                <span class="legend-item" style="background: rgba(10,90,53,.12); color: rgb(10, 90, 53); border-color: rgba(10,90,53,.25);">
                                    Izin ({{ (int) round(($monthlyAttendanceData['Izin'] / $total) * 100) }}%)
                                </span>
                            </div>
                        @endif
                        
                        <div class="chart-legend" style="margin-top: 10px; color: var(--text);">Periode: {{ Carbon::createFromDate($filterYear, $filterMonth, 1)->translatedFormat('F Y') }}</div>
                    </div>
                </div> {{-- End Kolom 2 --}}

            </div> {{-- End Chart Container Grid --}}

            <div style="text-align: center; margin-top: 30px; padding: 14px; border-top: 1px solid rgba(0,0,0,.06); color: var(--muted); font-weight: 800;">
                <p style="margin:0;">* Data diolah secara otomatis berdasarkan transaksi dan absensi yang dicatat.</p>
            </div>

        </div>

        <div class="footer">
            <div class="footer-left">
                <span><i class="fas fa-code"></i> Dibuat oleh UKM IT Team</span>
            </div>
            <div>
                <span>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA</span>
            </div>
        </div>
    </div>

<script>
    // Pastikan data ini dikirim oleh DashboardController.php
    const monthlyKas = @json($monthlyKas ?? ['labels' => [], 'pemasukan' => [], 'pengeluaran' => []]);
    const monthlyAttendanceData = @json($monthlyAttendanceData ?? []);
    const totalAttendance = Object.values(monthlyAttendanceData).reduce((sum, value) => sum + value, 0);
    const totalPresentText = "{{ $totalPresentText }}"; 

    // ======================================================================
    // PLUGIN CHART.JS (Menggambar Teks di Tengah Donut)
    // ======================================================================
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            if (chart.config.type === 'doughnut' && chart.getDatasetMeta(0).data.length > 0) {
                const ctx = chart.ctx;
                const width = chart.width;
                const height = chart.height;
                
                ctx.restore();
                
                const fontSize = (height / 114).toFixed(2);
                
                ctx.font = `${fontSize}em 'Plus Jakarta Sans', sans-serif`; 
                ctx.textBaseline = "middle";
                
                const text = totalPresentText; 
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2; 
                
                ctx.fillStyle = '#004225'; 
                ctx.fillText(text, textX, textY);
                ctx.save();
            }
        }
    };

    // ======================================================================
    // BAR CHART (Kas)
    // ======================================================================
    const kasCtx = document.getElementById('kasBarChart');
    if (kasCtx) {
        new Chart(kasCtx, {
            type: 'bar',
            data: {
                labels: monthlyKas.labels,
                datasets: [{
                    label: 'Pemasukan (Rp)',
                    data: monthlyKas.pemasukan,
                    backgroundColor: 'rgb(0, 150, 60)', 
                    hoverBackgroundColor: 'rgb(0, 170, 70)',
                }, {
                    label: 'Pengeluaran (Rp)',
                    data: monthlyKas.pengeluaran,
                    backgroundColor: 'rgb(220, 53, 69)', 
                    hoverBackgroundColor: 'rgb(240, 70, 90)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    } 
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // ======================================================================
    // DOUGHNUT (Absensi)
    // ======================================================================
    const absensiCtx = document.getElementById('absensiPieChart');

    if (absensiCtx && totalAttendance > 0) {
        const labels = Object.keys(monthlyAttendanceData);
        const dataValues = Object.values(monthlyAttendanceData);

        new Chart(absensiCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        'rgb(0, 150, 60)',     
                        'rgb(220, 53, 69)',  
                        'rgb(255, 215, 0)',  
                        'rgb(10, 90, 53)'    
                    ],
                    hoverBackgroundColor: [
                        'rgb(0, 170, 70)',
                        'rgb(240, 70, 90)',
                        'rgb(255, 225, 20)',
                        'rgb(20, 110, 63)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%', 
                rotation: -90, 
                circumference: 360,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.parsed;
                                const percentage = (value / totalAttendance * 100).toFixed(1) + '%';
                                label += `${value} (${percentage})`;
                                return label;
                            }
                        }
                    }
                }
            },
            plugins: [centerTextPlugin] 
        });
    }
</script>

</body>
</html>