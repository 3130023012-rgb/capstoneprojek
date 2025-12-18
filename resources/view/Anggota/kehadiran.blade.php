@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kehadiran - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700; --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb; --success:#16a34a; --warning:#facc15; --danger:#ef4444; --info:#3b82f6; --radius:16px; --shadow:0 14px 30px rgba(0,0,0,.08); --font:"Plus Jakarta Sans", system-ui, sans-serif;
        }
        *{box-sizing:border-box}
        
        /* FIX: Body flex untuk sidebar dan content */
        body{
            margin:0; font-family:var(--font); min-height: 100vh; 
            display: flex; 
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%), radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg);
            color:var(--text);
        }
        
        /* --- SIDEBAR --- */
        .sidebar{ 
            width: 240px; 
            min-height: 100vh;
            background: var(--card); 
            box-shadow: 2px 0 12px rgba(0,0,0,.04); 
            border-right: 1px solid var(--border); 
            flex-shrink: 0; 
            position: sticky;
            top: 0;
        }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: #fff; background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding:10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); width: 20px; text-align: center; }
        .sidebar-menu > li > a { display:flex; align-items:center; padding:12px 16px; text-decoration:none; font-size:15px; color:#0f172a; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu a:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu a.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-menu a.active i { color: var(--primary); }

        /* --- CONTENT WRAPPER --- */
        .content-wrapper { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            min-width: 0; 
        }
        
        .header-top{ 
            background: var(--card); 
            padding: 16px 22px; 
            border-bottom: 1px solid var(--border); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,.06); 
        }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        
        .profile-access{ 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            text-decoration: none; 
            padding: 8px 15px; 
            border-radius: 999px; 
            border: 1px solid rgba(0,66,37,.12); 
            background: rgba(0,66,37,.05); 
            color: var(--primary); 
            font-weight: 900; 
        }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; font-size: 1.05em; }

        .main-content { padding: 30px 26px; }
        .page-title { text-align: center; margin-bottom: 25px; color: var(--primary); font-weight: 900; }

        /* --- SUMMARY GRID --- */
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .summary-item { padding: 20px; border-radius: 14px; text-align: center; box-shadow: var(--shadow); position: relative; overflow: hidden; }
        .summary-item h3 { margin: 10px 0 0; font-size: 1.8em; font-weight: 900; }
        .summary-item span { font-weight: 800; font-size: 0.9em; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .bg-success { background: #16a34a; color: #fff; }
        .bg-warning { background: var(--accent); color: var(--primary); }
        .bg-danger { background: #dc2626; color: #fff; }
        .bg-info { background: #2563eb; color: #fff; }

        /* --- DATA TABLE --- */
        .data-box { background: var(--card); padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); }
        .table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th { background: #f8fafc; color: var(--primary); font-weight: 800; padding: 15px; text-align: left; font-size: 0.9em; border-bottom: 2px solid var(--border); }
        td { padding: 15px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 700; font-size: 0.95em; vertical-align: middle; }
        tbody tr:hover { background: rgba(0,66,37,0.02); }

        .badge { display: inline-flex; padding: 6px 12px; border-radius: 999px; font-weight: 900; font-size: 0.75em; text-transform: uppercase; }
        .badge.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge.warning { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
        .badge.danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge.info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        .pagination-container { margin-top: 25px; display: flex; justify-content: center; }

        .footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); margin-top: auto; }

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
            <li><a href="{{ route('anggota.index') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('anggota.kehadiran') }}" class="active"><i class="fas fa-calendar-check"></i> Status Kehadiran</a></li>
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
            <h1>Detail Kehadiran</h1>
            <div class="profile-access">
                <span>{{ $member->name ?? Auth::user()->name ?? 'ANGGOTA' }}</span>
                <span class="profile-icon-circle"><i class="fas fa-user"></i></span>
            </div>
        </div>

        <div class="main-content">
            <h2 class="page-title">Rekapitulasi Kehadiran Total</h2>

            <div class="summary-grid">
                <div class="summary-item bg-success"><span>Hadir</span><h3>{{ $totalHadir ?? 0 }}x</h3></div>
                <div class="summary-item bg-warning"><span>Izin</span><h3>{{ $totalIzin ?? 0 }}x</h3></div>
                <div class="summary-item bg-info"><span>Sakit</span><h3>{{ $totalSakit ?? 0 }}x</h3></div>
                <div class="summary-item bg-danger"><span>Absen</span><h3>{{ $totalAbsen ?? 0 }}x</h3></div>
            </div>

            <div class="data-box">
                <h3 style="color: var(--primary); margin-top: 0; margin-bottom: 20px; font-weight: 900;"><i class="fas fa-history"></i> Riwayat Kegiatan Latihan</h3>
                
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Tanggal</th>
                                <th style="width: 55%;">Materi / Nama Kegiatan</th>
                                <th style="width: 20%; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $badgeMap = [
                                    'present'    => 'success',
                                    'permission' => 'warning',
                                    'sick_leave' => 'info',
                                    'absent'     => 'danger'
                                ];
                                $statusText = [
                                    'present'    => 'Hadir',
                                    'permission' => 'Izin',
                                    'sick_leave' => 'Sakit',
                                    'absent'     => 'Absen'
                                ];
                            @endphp
                            @forelse ($attendances as $attendance)
                            <tr>
                                <td style="color: var(--primary); font-weight: 800;">
                                    <i class="far fa-calendar-alt" style="margin-right: 8px; opacity: 0.5;"></i>
                                    {{ $attendance->activity ? \Carbon\Carbon::parse($attendance->activity->date)->translatedFormat('d F Y') : 'N/A' }}
                                </td>
                                <td style="font-weight: 800; color: var(--primary);">{{ $attendance->activity->material ?? 'Kegiatan Dihapus' }}</td>
                                <td style="text-align: center;">
                                    <span class="badge badge-{{ $badgeMap[$attendance->status] ?? 'secondary' }}">
                                        {{ $statusText[$attendance->status] ?? ucfirst($attendance->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px; color: var(--muted);">
                                    <i class="fas fa-search fa-2x" style="display:block; margin-bottom:10px; opacity:0.3;"></i>
                                    Belum ada riwayat kehadiran yang tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(isset($attendances) && $attendances->lastPage() > 1)
                    <div class="pagination-container">
                        {{ $attendances->links() }} 
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

</body>
</html>