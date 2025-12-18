@php
    use Carbon\Carbon;

    /* |--------------------------------------------------------------------------
    | LOGIKA FILTER & DATA
    |--------------------------------------------------------------------------
    | Mengambil data dari request atau variabel kiriman Controller.
    */
    $monthFilter = request('month_filter', Carbon::now()->format('Y-m'));
    $dateFilter = request('date_filter');
    
    // Tentukan Judul Berdasarkan Filter yang Aktif
    if ($dateFilter) {
        $titleDateText = Carbon::parse($dateFilter)->translatedFormat('d F Y');
    } else {
        $titleDateText = Carbon::parse($monthFilter)->translatedFormat('F Y');
    }

    // Pastikan $detailedAttendance selalu berupa collection/array agar tidak error
    $attendanceData = $detailedAttendance ?? collect([]);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UKM PAGARNUSA - Manajemen Absensi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #004225;
            --primary-2: #0a5a35;
            --accent: #FFD700;
            --bg: #f6faf7;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e5e7eb;
            --shadow-sm: 0 2px 10px rgba(0,0,0,.06);
            --shadow-md: 0 14px 30px rgba(0,0,0,.10);
            --radius: 16px;
            --color-danger: #dc3545;
            --color-white: #fff;
            --font: "Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font);
            margin: 0;
            min-height: 100vh;
            display: flex;
            color: var(--text);
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%),
                        radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%),
                        var(--bg);
        }

        /* --- SIDEBAR --- */
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); flex-shrink: 0; }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: var(--color-white); background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding: 10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); }
        .sidebar-menu summary::-webkit-details-marker { display:none; }
        .sidebar-menu summary, .sidebar-menu > li > a { display:flex; align-items:center; padding:12px 16px; font-size:15px; color:#0f172a; cursor:pointer; border-left:4px solid transparent; text-decoration:none; transition:.15s ease; font-weight:700; }
        .sidebar-menu summary:hover, .sidebar-menu > li > a:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu summary::after{ content:'▼'; margin-left:auto; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
        .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }
        .sidebar-menu a.active, .sidebar-menu summary.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-menu a.active i, .sidebar-menu summary.active i { color: var(--primary); }
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; margin:2px 12px 2px 0; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* --- CONTENT WRAPPER --- */
        .content-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.5em; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); font-size: 1.05em; }

        .main-content-area { padding: 30px 26px 20px; flex-grow: 1; }
        .page-header-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow-md); margin-bottom: 25px; position: relative; overflow: hidden; }
        .page-header-box::before{ content:""; position:absolute; top:-60px; right:-60px; width:180px; height:180px; border-radius:28px; opacity:.18; transform: rotate(12deg); background: radial-gradient(circle at 30% 30%, var(--accent), transparent 60%); }
        .page-header-box h2 { margin: 0; font-size: 1.8em; font-weight: 950; color: var(--primary); }
        
        .section-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); padding: 20px; margin-bottom: 20px; }

        /* --- ALERTS --- */
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; border: 1px solid transparent; }
        .alert.success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .alert.info { background: #fef9c3; color: #854d0e; border-color: #fef08a; }

        /* --- FILTERS & BUTTONS --- */
        .filter-group select, .filter-group input { padding: 10px 15px; border-radius: 12px; border: 1px solid var(--border); font-family: var(--font); font-weight: 800; color: var(--text); outline: none; }
        .filter-group select:focus, .filter-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,66,37,0.1); }
        
        .actions-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .btn-action { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 999px; font-weight: 950; text-decoration: none; transition: 0.2s; font-size: 0.9em; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #fff; box-shadow: 0 10px 20px rgba(0,66,37,0.15); }
        .btn-accent { background: rgba(255,215,0,0.15); color: #856404; border: 1px solid rgba(255,215,0,0.3); }
        .btn-action:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .search-form input { padding: 10px 15px; border-radius: 12px 0 0 12px; border: 1px solid var(--border); border-right: none; font-weight: 700; outline: none; }
        .search-form button { padding: 10px 20px; border-radius: 0 12px 12px 0; border: 1px solid var(--primary); background: var(--primary); color: #fff; font-weight: 800; cursor: pointer; }

        /* --- TABLE --- */
        .table-responsive { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        thead th { background: #f8fafc; color: var(--primary); padding: 15px; font-size: .9em; text-align: left; font-weight: 800; border-bottom: 2px solid var(--border); }
        tbody td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-weight: 600; font-size: 0.95em; vertical-align: middle; }
        tbody tr:hover { background: rgba(0,66,37,0.02); }

        /* --- BADGES --- */
        .badge { display: inline-flex; padding: 5px 12px; border-radius: 999px; font-size: .75em; font-weight: 900; text-transform: uppercase; }
        .badge.success { background: #dcfce7; color: #166534; }
        .badge.warning { background: #fef9c3; color: #854d0e; }
        .badge.danger { background: #fee2e2; color: #991b1b; }
        .badge.primary { background: #dbeafe; color: #1e40af; }

        .footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); display: flex; justify-content: center; gap: 20px; }
        
        @media (max-width: 900px) { .sidebar { display: none; } }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="sb-title">Admin UKM</div>
        <div class="sb-subtitle">PAGARNUSA</div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="{{ route('dashboard.index') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li>
            <details>
                <summary><i class="fas fa-users-cog"></i> Master Data Pengguna</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('member.index') }}">Manajemen Anggota</a></li>
                    <li><a href="{{ route('konten.index') }}">Manajemen Konten</a></li>
                </ul>
            </details>
        </li>
        <li><a href="{{ route('absensi.index') }}" class="active"><i class="fas fa-clipboard-check"></i> Absensi</a></li>
        <li>
            <details>
                <summary><i class="fas fa-cash-register"></i> Transaksi</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('kas.index') }}">Kas</a></li>
                    <li><a href="{{ route('kegiatan.index') }}">Kegiatan</a></li>
                </ul>
            </details>
        </li>
        <li>
            <details>
                <summary><i class="fas fa-file-alt"></i> Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}">Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}">Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li>
                </ul>
            </details>
        </li>
        <li style="margin-top: 20px; border-top: 1px solid #eee;">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger); font-weight: 900;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

<div class="content-wrapper">
    <div class="header-top">
        <h1><i class="fas fa-clipboard-list" style="margin-right: 10px;"></i> Dashboard Absensi</h1>
        <a href="{{ route('profile.edit') }}" class="profile-access">
            <span class="profile-name">Admin</span>
            <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
        </a>
    </div>

    <div class="main-content-area">
        @if (session('success')) <div class="alert success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div> @endif
        @if (session('info')) <div class="alert info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div> @endif

        <div class="page-header-box">
            <h2>Manajemen Absensi</h2>
            <p>Data Kehadiran Terpusat UKM PAGARNUSA UNUSA</p>
        </div>

        <div class="section-box">
            <form action="{{ route('absensi.index') }}" method="GET" class="filter-group" style="display:flex; flex-wrap:wrap; gap:20px; align-items:flex-end;">
                <div>
                    <label style="font-weight:800; display:block; margin-bottom:8px; color:var(--primary);">Filter Bulan:</label>
                    <select name="month_filter" onchange="this.form.submit()">
                        @for ($i = 0; $i < 12; $i++)
                            @php
                                $dateOption = Carbon::now()->subMonths($i);
                                $val = $dateOption->format('Y-m');
                            @endphp
                            <option value="{{ $val }}" {{ $monthFilter == $val ? 'selected' : '' }}>
                                {{ $dateOption->translatedFormat('F Y') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label style="font-weight:800; display:block; margin-bottom:8px; color:var(--primary);">Tanggal Spesifik:</label>
                    <input type="date" name="date_filter" value="{{ $dateFilter }}" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="actions-row">
            <a href="{{ route('absensi.create') }}" class="btn-action btn-primary"><i class="fas fa-plus"></i> Tambah Kehadiran</a>
            <a href="{{ route('absensi.select') }}" class="btn-action btn-accent"><i class="fas fa-edit"></i> Perbarui Data</a>
        </div>

        <div class="section-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; flex-wrap:wrap; gap:15px;">
                <h3 style="margin:0; font-weight:900; color:var(--primary);">
                    <i class="fas fa-calendar-day"></i> Periode: {{ $titleDateText }}
                </h3>

                <form action="{{ route('absensi.index') }}" method="GET" class="search-form" style="display:flex;">
                    <input type="hidden" name="month_filter" value="{{ $monthFilter }}">
                    <input type="hidden" name="date_filter" value="{{ $dateFilter }}">
                    <input type="text" name="search" placeholder="Cari nama/NIM..." value="{{ request('search') }}">
                    <button type="submit"><i class="fas fa-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('absensi.index', ['month_filter' => $monthFilter, 'date_filter' => $dateFilter]) }}" style="align-self:center; margin-left:10px; font-weight:800; color:var(--color-danger); text-decoration:none;">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>NIM / NIA</th>
                            <th>Program Studi</th>
                            <th>Pelatih</th>
                            <th>Tanggal</th>
                            <th>Materi</th>
                            <th style="text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendanceData as $item)
                        <tr>
                            <td style="font-weight:800; color: var(--primary);">{{ $item->member->name ?? 'N/A' }}</td>
                            <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-family:monospace;">{{ $item->member->member_id ?? '-' }}</span></td>
                            <td>{{ $item->member->study_program ?? '-' }}</td>
                            <td>{{ $item->activity->trainer->name ?? 'N/A' }}</td>
                            <td>{{ Carbon::parse($item->activity->date)->translatedFormat('d M Y') }}</td>
                            <td>{{ $item->activity->material }}</td>
                            <td style="text-align:center;">
                                @php
                                    $status = $item->status;
                                    $label = [
                                        'present' => ['Hadir', 'success'],
                                        'absent' => ['Absen', 'danger'],
                                        'sick_leave' => ['Sakit', 'warning'],
                                        'permission' => ['Izin', 'primary']
                                    ][$status] ?? ['Lainnya', 'muted'];
                                @endphp
                                <span class="badge {{ $label[1] }}">{{ $label[0] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:50px; color:var(--muted);">
                                <i class="fas fa-folder-open fa-3x" style="margin-bottom:15px; opacity:0.3;"></i><br>
                                Data tidak ditemukan untuk filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <span><i class="fas fa-copyright"></i> {{ date('Y') }} UKM PAGARNUSA</span>
        <span><i class="fas fa-shield-alt"></i> Wira Laga Santri</span>
    </div>
</div>

</body>
</html>