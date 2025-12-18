@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Member; 
    use App\Models\Activity;
    use App\Models\KasTransaction;

    Carbon::setLocale('id');

    // --- Ambil Data Summary ---
    $totalMembers = Member::count(); 
    $totalActivities = Activity::count(); 

    $totalIn = KasTransaction::where('type', 'in')->sum('amount');
    $totalOut = KasTransaction::where('type', 'out')->sum('amount');
    $totalKas = $totalIn - $totalOut;

    $latestActivities = Activity::orderBy('date', 'desc')->limit(5)->get();

    $user = Auth::user();
    
    // Pembersihan nama pembina
    $fullName = $user->name ?? 'Pengguna';
    $cleanName = trim(str_ireplace('Pembina', '', $fullName)); 
    $displayName = empty($cleanName) ? 'Pengguna' : $cleanName;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembina - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-success: #28a745; --color-danger: #dc3545; --color-white: #fff;
            --font: "Plus Jakarta Sans", system-ui, sans-serif;
        }

        *{ box-sizing:border-box; }

        body{
            font-family: var(--font); margin: 0; min-height: 100vh; display: flex; color: var(--text);
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%),
            radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg);
        }

        /* --- SIDEBAR (Sesuai Desain Admin) --- */
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); flex-shrink: 0; }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: var(--color-white); background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        
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
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* --- CONTENT WRAPPER --- */
        .content-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); font-size: 1.05em; }

        .main-content-area{ padding: 30px 26px; flex-grow: 1; }

        /* --- DASHBOARD ELEMENTS --- */
        .section-title{ color: #0f172a; font-size: 1.8em; font-weight: 950; margin: 0 0 24px; border-bottom: 2px solid var(--accent); padding-bottom: 8px; display:inline-block; }

        .metrics-grid{ display:grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .metric-card{ border: 1px solid rgba(0,66,37,.15); padding: 20px; border-radius: 20px; background: var(--card); box-shadow: var(--shadow-sm); position: relative; overflow:hidden; transition: transform .2s ease; }
        .metric-card:hover { transform: translateY(-3px); }
        .metric-card small{ font-size: 0.9em; color: var(--muted); display:block; margin-bottom: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .metric-card p{ font-size: 1.8em; font-weight: 950; margin: 0; color: var(--primary); }
        .metric-card i { position: absolute; top: 20px; right: 20px; font-size: 2.2em; color: var(--accent); opacity: 0.7; }

        .panel-card{ background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-md); padding: 25px; margin-bottom: 25px; }
        
        .table-responsive{ overflow-x:auto; border-radius: 12px; border: 1px solid var(--border); }
        table{ width:100%; border-collapse: collapse; background:#fff; }
        thead th{ background: #f8fafc; color: var(--primary); padding: 15px; text-align: left; font-weight: 800; border-bottom: 2px solid var(--border); }
        tbody td{ padding: 15px; border-bottom: 1px solid #f1f5f9; font-weight: 600; vertical-align: middle; }

        .badge{ display:inline-flex; padding:5px 12px; border-radius: 999px; font-size: 0.8em; font-weight: 800; }
        .badge.success{ background: #dcfce7; color: #166534; }
        .badge.warning{ background: #fef9c3; color: #854d0e; }
        .badge.danger{ background: #fee2e2; color: #991b1b; }
        .badge.secondary{ background: #f1f5f9; color: #475569; }
        .badge.primary{ background: #dbeafe; color: #1e40af; }

        .footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); }

        @media (max-width: 900px){
            .sidebar { display: none; }
            .metrics-grid{ grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sb-title">UKM</div>
            <div class="sb-subtitle">PAGARNUSA</div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('pembina.index') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li>
                <details open>
                    <summary><i class="fas fa-file-alt"></i> Laporan UKM</summary>
                    <ul class="sidebar-dropdown">
                        <li><a href="{{ route('pembina.reports.cash') }}">Laporan Kas</a></li>
                        <li><a href="{{ route('pembina.reports.attendance') }}">Laporan Absensi</a></li>
                        <li><a href="{{ route('pembina.reports.activity') }}">Laporan Kegiatan</a></li>
                    </ul>
                </details>
            </li>
            <li style="margin-top: 20px; border-top: 1px solid #eee;">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="content-wrapper">
        <div class="header-top">
            <h1><i class="fas fa-user-tie" style="margin-right: 10px;"></i> Dashboard Pembina</h1>
            <a href="#" class="profile-access">
                <span class="profile-name">Halo, {{ $displayName }}</span>
                <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </a>
        </div>

        <div class="main-content-area">
            <h2 class="section-title">Ringkasan Statistik UKM</h2>

            <div class="metrics-grid">
                <div class="metric-card">
                    <i class="fas fa-users"></i>
                    <small>Anggota Aktif</small>
                    <p>{{ number_format($totalMembers, 0, ',', '.') }}</p>
                </div>
                <div class="metric-card">
                    <i class="fas fa-calendar-check"></i>
                    <small>Total Kegiatan</small>
                    <p>{{ number_format($totalActivities, 0, ',', '.') }}</p>
                </div>
                <div class="metric-card">
                    <i class="fas fa-wallet"></i>
                    <small>Saldo Kas UKM</small>
                    <p>Rp {{ number_format($totalKas, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="panel-card" style="background: rgba(59,130,246,0.05); border-left: 5px solid #3b82f6;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #1e40af; font-size: 1.1em;"><i class="fas fa-info-circle"></i> Notifikasi Penting</strong>
                    <span class="badge primary">Review Dibutuhkan</span>
                </div>
                <p style="margin: 10px 0 0; color: #475569; font-weight: 600;">
                    Silakan tinjau laporan absensi dan realisasi anggaran kas yang telah diperbarui oleh pengurus untuk periode bulan ini.
                </p>
            </div>

            <div class="panel-card">
                <h3 style="margin-top: 0; color: var(--primary); font-weight: 900;"><i class="fas fa-history"></i> 5 Kegiatan Terbaru</h3>
                <p style="color: var(--muted); font-weight: 600; margin-bottom: 20px;">Daftar aktivitas latihan atau agenda eksternal yang baru saja dilaksanakan.</p>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Materi / Kegiatan</th>
                                <th>Anggaran Diajukan</th>
                                <th style="text-align: center;">Status Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestActivities as $activity)
                                @php
                                    $date = \Carbon\Carbon::parse($activity->date);
                                    $confirmStatus = $activity->confirmation_status ?? 'pending';
                                    $confirmClass = $confirmStatus == 'confirmed' ? 'success' : ($confirmStatus == 'rejected' ? 'danger' : 'warning');
                                @endphp
                                <tr>
                                    <td>{{ $date->translatedFormat('d M Y') }}</td>
                                    <td style="color: var(--primary); font-weight: 800;">{{ $activity->material }}</td>
                                    <td>Rp {{ number_format($activity->nominal ?? 0, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge {{ $confirmClass }}">
                                            {{ ucfirst($confirmStatus) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 30px; color: var(--muted);">Belum ada data kegiatan terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="footer">
            <span><i class="fas fa-copyright"></i> {{ date('Y') }} UKM PAGARNUSA UNUSA</span>
            <span><i class="fas fa-shield-alt"></i> Hubbul Wathon Minal Iman</span>
        </div>
    </div>
</body>
</html>