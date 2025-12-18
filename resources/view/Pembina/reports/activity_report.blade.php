@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    
    // Pembersihan nama pembina untuk header
    $fullName = $user->name ?? 'Pengguna';
    $cleanName = trim(str_ireplace('Pembina', '', $fullName)); 
    $displayName = empty($cleanName) ? 'Pengguna' : $cleanName;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kegiatan Pembina - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
    :root{
        --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700; 
        --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b;
        --border:#e5e7eb; --success:#16a34a; --warning:#facc15; 
        --danger:#ef4444; --info:#3b82f6; --radius:16px;
        --font:"Plus Jakarta Sans", system-ui, sans-serif;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0; font-family: var(--font); color: var(--text);
        background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%), 
                    radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg); 
    }
    .main-container{ display: flex; min-height: 100vh; }
    
    /* --- SIDEBAR --- */
    .sidebar { width: 240px; background: var(--card); border-right: 1px solid var(--border); box-shadow: 2px 0 12px rgba(0,0,0,.04); flex-shrink: 0; }
    .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: #fff; background: linear-gradient(135deg,var(--primary),var(--primary-2)); position: relative; letter-spacing: .2px; }
    .sidebar-header::after{ content: ""; position: absolute; left: 16px; right: 16px; bottom: 10px; height: 3px; border-radius: 999px; background: linear-gradient(90deg,transparent,var(--accent),transparent); opacity: .95; }
    .sidebar-menu{ list-style:none; padding:10px 0; margin:0; }
    .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); width: 20px; text-align: center; }
    .sidebar-menu > li > a, .sidebar-menu summary { display:flex; align-items:center; padding:12px 16px; text-decoration:none; font-size:15px; color:#0f172a; border-left:4px solid transparent; transition:.15s ease; font-weight:700; cursor: pointer; }
    .sidebar-menu a:hover, .sidebar-menu summary:hover { background: rgba(0,66,37,.06); }
    .sidebar-menu a.active, .sidebar-menu summary.active { background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
    
    .sidebar-menu summary::-webkit-details-marker { display:none; }
    .sidebar-menu summary::after{ content:'▼'; margin-left:auto; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
    .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }
    .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
    .sidebar-dropdown a{ display:block; padding:8px 12px; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }

    /* --- CONTENT --- */
    .content-wrapper { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .header-top { background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm); }
    .header-top h1 { margin: 0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing: .2px; }
    .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
    .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; font-size: 1.05em; }

    .main-content { padding: 30px 26px; flex-grow: 1; }
    .section-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 25px; box-shadow: var(--shadow-md); margin-bottom: 25px; }

    /* --- FILTERS --- */
    .filter-row { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; margin-bottom: 25px; padding: 15px; background: #f8fafc; border-radius: 12px; border: 1px solid var(--border); }
    .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px; }
    .filter-group label { font-size: 0.85em; font-weight: 800; color: var(--muted); text-transform: uppercase; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 10px; font-weight: 700; font-family: var(--font); outline: none; }
    .btn { padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 800; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; font-family: var(--font); }
    .btn-primary { background: var(--primary); color: white; }
    .btn-secondary { background: #e2e8f0; color: var(--text); text-decoration: none; }

    /* --- TABLE --- */
    .table-responsive { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
    .table { width: 100%; border-collapse: collapse; background: #fff; }
    .table thead th { background: #f8fafc; color: var(--primary); padding: 15px; text-align: left; font-weight: 800; border-bottom: 2px solid var(--border); font-size: 0.9em; }
    .table tbody td { padding: 14px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95em; font-weight: 600; vertical-align: middle; }
    
    .badge { padding: 4px 12px; border-radius: 999px; font-size: 0.75em; font-weight: 800; text-transform: uppercase; }
    .badge-success { background: #dcfce7; color: #166534; }
    .badge-warning { background: #fef9c3; color: #854d0e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }

    .footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); }

    @media (max-width: 900px){ .sidebar { display: none; } }
    </style>
</head>
<body>

<div class="main-container">

    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sb-title">UKM</div>
            <div class="sb-subtitle">Pembina</div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('pembina.index') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li>
                <details open>
                    <summary class="active"><i class="fas fa-file-alt"></i> Laporan UKM</summary>
                    <ul class="sidebar-dropdown">
                        <li><a href="{{ route('pembina.reports.cash') }}">Laporan Kas</a></li>
                        <li><a href="{{ route('pembina.reports.attendance') }}">Laporan Absensi</a></li>
                        <li><a href="{{ route('pembina.reports.activity') }}" class="active">Laporan Kegiatan</a></li>
                    </ul>
                </details>
            </li>
            <li style="margin-top: 20px; border-top: 1px solid #eee;">
                <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="color: var(--danger);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="content-wrapper">
        <div class="header-top">
            <h1><i class="fas fa-calendar-alt" style="margin-right: 10px;"></i> Laporan Kegiatan</h1>
            <div class="profile-access">
                <span class="profile-name">Halo, {{ $displayName }}</span>
                <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </div>
        </div>

        <div class="main-content">
            <div class="section-card">
                <h2 style="margin-bottom: 20px;"><i class="fas fa-filter"></i> Filter Periode Kegiatan</h2>
                
                <form method="GET" class="filter-row">
                    <div class="filter-group">
                        <label for="start_date">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                    </div>
                    <div class="filter-group">
                        <label for="end_date">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('pembina.reports.activity') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tanggal</th>
                                <th>Materi / Nama Kegiatan</th>
                                <th style="text-align: center;">Target Anggota</th>
                                <th style="text-align: center;">Hadir</th>
                                <th style="text-align: right;">Anggaran (IDR)</th>
                                <th style="text-align: center;">Status Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                @php
                                    $confirmStatus = $activity->confirmation_status ?? 'pending';
                                    $confirmClass = $confirmStatus == 'confirmed' ? 'success' : ($confirmStatus == 'pending' ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ Carbon::parse($activity->date)->format('d-m-Y') }}</td>
                                    <td style="color: var(--primary); font-weight: 800;">{{ $activity->material }}</td>
                                    <td style="text-align: center;">{{ $activity->total_members }}</td>
                                    <td style="text-align: center;">{{ $activity->attendances_count }}</td>
                                    <td style="text-align: right; font-weight: 800;">Rp {{ number_format($activity->nominal, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge {{ $confirmClass }}">
                                            {{ ucfirst($confirmStatus) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--muted);">
                                        <i class="fas fa-calendar-times fa-3x" style="display:block; margin-bottom:10px; opacity:0.2;"></i>
                                        Tidak ada data kegiatan dalam periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($activities->isNotEmpty())
                <div style="margin-top: 20px;">
                    {{ $activities->links() }}
                </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <span>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA</span>
            <span><i class="fas fa-shield-alt"></i> Wira Laga Santri</span>
        </div>
    </div>
</div>

</body>
</html>