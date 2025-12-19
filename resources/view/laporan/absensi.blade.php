@php
    use Carbon\Carbon;
    $filterDate = request('filter_month', Carbon::now()->format('Y-m'));
    $titleDate = Carbon::createFromFormat('Y-m', $filterDate);
    
    // Pastikan variabel dikirim dari controller
    $activities = $activities ?? collect();
    $members = $members ?? collect();
    $attendances = $attendances ?? collect();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - UKM PAGARNUSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-danger: #dc3545; --color-success: #28a745; --font: "Plus Jakarta Sans", sans-serif;
        }
        *{ box-sizing:border-box; }
        body{ 
            font-family: var(--font); margin: 0; min-height: 100vh; display: flex; color: var(--text); 
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%),
                        radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg); 
        }
        
        /* --- SIDEBAR (Identik Dashboard) --- */
        .sidebar{ width: 240px; background: var(--card); border-right: 1px solid var(--border); box-shadow: 2px 0 12px rgba(0,0,0,.04); flex-shrink: 0; }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: #fff; background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding: 10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); width: 20px; text-align: center; }
        .sidebar-menu summary::-webkit-details-marker { display:none; }
        .sidebar-menu summary, .sidebar-menu > li > a { display:flex; align-items:center; padding:12px 16px; font-size:15px; color:#0f172a; cursor:pointer; border-left:4px solid transparent; text-decoration:none; transition:.15s ease; font-weight:700; }
        .sidebar-menu summary:hover, .sidebar-menu > li > a:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu summary::after{ content:'▼'; margin-left:auto; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
        .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }
        .sidebar-menu a.active, .sidebar-menu summary.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-menu a.active i, .sidebar-menu summary.active i { color: var(--primary); }
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* --- CONTENT WRAPPER --- */
        .content{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; font-size: 1.05em; }

        .main-content-area{ padding: 30px 26px 20px; }
        .section-box{ background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-md); padding: 30px; }
        
        /* HEADER LAPORAN */
        .report-header { text-align: center; margin-bottom: 30px; }
        .report-title { color: var(--primary); font-weight: 950; margin: 0 auto 10px; display: inline-block; border-bottom: 3px solid var(--accent); padding-bottom: 5px; line-height: 1.4; font-size: 1.8em; }
        .report-period { color: var(--muted); font-weight: 700; font-size: 1.1em; }

        .filter-row{ display:flex; justify-content:space-between; align-items:center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
        .btn-print{ background: var(--accent); color: var(--primary); text-decoration: none; padding: 10px 22px; border-radius: 999px; font-weight: 900; display: inline-flex; align-items:center; gap: 8px; box-shadow: 0 10px 20px rgba(255,215,0,0.2); transition: 0.2s; }
        .btn-print:hover{ transform: translateY(-2px); filter: brightness(1.05); }

        /* TABLE STYLE */
        .table-responsive{ overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
        table{ width: 100%; border-collapse: collapse; background: #fff; }
        th{ background: #f8fafc; color: var(--primary); font-weight: 800; padding: 15px 12px; border: 1px solid var(--border); text-align: center; font-size: 0.9em; }
        td{ padding: 12px; border: 1px solid var(--border); text-align: center; font-size: 0.95em; font-weight: 700; }
        tbody tr:hover{ background: rgba(0,66,37,0.02); }
        
        .status-h{ color: var(--color-success); }
        .status-a{ color: var(--color-danger); }

        @media print{ 
            .sidebar, .header-top, .filter-row, .btn-print{ display: none !important; } 
            body{ background: #fff; } 
            .section-box{ box-shadow: none; border: none; padding: 0; } 
            .report-title { font-size: 20pt; }
            table { width: 100% !important; border: 1px solid #333 !important; }
            th, td { border: 1px solid #333 !important; color: #000 !important; }
        }
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
        <li><a href="{{ route('absensi.index') }}"><i class="fas fa-clipboard-check"></i> Absensi</a></li>
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
            <details open>
                <summary class="active"><i class="fas fa-file-alt"></i> Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}" class="active">Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}">Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li>
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

    <div class="content">
        <div class="header-top">
            <h1><i class="fas fa-clipboard-list" style="margin-right: 10px;"></i> Laporan Administrasi</h1>
            <div class="profile-access">
                Admin <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </div>
        </div>

        <div class="main-content-area">
            <div class="section-box">
                <div class="filter-row">
                    <form method="GET" action="{{ route('laporan.absensi') }}" style="display:flex; gap:10px;">
                        <select name="filter_month" style="padding: 10px 15px; border-radius: 12px; border: 1px solid var(--border); font-weight: 700; outline:none;">
                            @for ($i = 0; $i < 12; $i++)
                                @php $date = Carbon::now()->subMonths($i); $val = $date->format('Y-m'); @endphp
                                <option value="{{ $val }}" {{ $filterDate == $val ? 'selected' : '' }}>
                                    {{ $date->translatedFormat('F Y') }}
                                </option>
                            @endfor
                        </select>
                        <button type="submit" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.2s;">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                    <a href="javascript:window.print()" class="btn-print">
                        <i class="fas fa-file-pdf"></i> Cetak Laporan (PDF)
                    </a>
                </div>

                <div class="report-header">
                    <h2 class="report-title">
                        REKAPITULASI KEHADIRAN ANGGOTA <br>
                        UKM PAGAR NUSA UNUSA
                    </h2>
                    <p class="report-period">Bulan: {{ $titleDate->translatedFormat('F Y') }}</p>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2" style="text-align: left; padding-left: 20px;">Nama Lengkap Anggota</th>
                                <th colspan="{{ max(1, $activities->count()) }}">Tanggal Latihan / Kegiatan</th>
                            </tr>
                            <tr>
                                @forelse($activities as $act)
                                    <th>{{ Carbon::parse($act->date)->format('d/m') }}</th>
                                @empty
                                    <th>-</th>
                                @endforelse
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr>
                                <td style="text-align: left; padding-left: 20px; color: var(--primary);">
                                    {{ $member->name }}
                                </td>
                                @forelse($activities as $act)
                                    @php
                                        $attn = $attendances->where('member_id', $member->id)
                                                            ->where('activity_id', $act->id)
                                                            ->first();
                                        
                                        $map = [
                                            'present' => 'H', 
                                            'absent' => 'A', 
                                            'sick_leave' => 'S', 
                                            'permission' => 'I'
                                        ];
                                        
                                        $status = $attn->status ?? null;
                                        $color = '';
                                        if($status == 'present') $color = 'var(--color-success)';
                                        elseif($status == 'absent') $color = 'var(--color-danger)';
                                        elseif($status == 'sick_leave') $color = '#f59e0b';
                                        elseif($status == 'permission') $color = '#3b82f6';
                                    @endphp
                                    <td style="color: {{ $color }};">
                                        {{ $map[$status] ?? '-' }}
                                    </td>
                                @empty
                                    <td style="color: #ccc;">-</td>
                                @endforelse
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 25px; font-size: 0.85em; color: var(--muted); font-weight: 700;">
                    <p>Keterangan: H (Hadir), A (Alpha), S (Sakit), I (Izin)</p>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
