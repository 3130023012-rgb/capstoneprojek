<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Anggota: {{ $member->name }} - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-white: #fff;
            --font: "Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        *{ box-sizing:border-box; }

        body{
            font-family: var(--font); margin: 0; min-height: 100vh; display: flex; color: var(--text);
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%),
            radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg);
        }

        /* --- SIDEBAR --- */
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); flex-shrink: 0; }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: var(--color-white); background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding: 10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); }
        .sidebar-menu > li > a { display:flex; align-items:center; padding:12px 16px; text-decoration:none; font-size:15px; color:#0f172a; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu a.active, .sidebar-menu summary.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        
        .sidebar-menu summary::-webkit-details-marker { display:none; }
        .sidebar-menu summary{ list-style:none; display:flex; justify-content:space-between; align-items:center; padding:12px 16px; font-size:15px; color:#0f172a; cursor:pointer; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu summary::after{ content:'▼'; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
        .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* --- CONTENT --- */
        .content-area{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .header-top-nav{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top-nav h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); }

        .main-container{ padding: 30px 26px; max-width: 1100px; }

        .profile-card{ background: rgba(255,255,255,0.78); border: 1px solid rgba(15,23,42,0.06); border-radius: var(--radius); box-shadow: var(--shadow-md); padding: 22px; backdrop-filter: blur(6px); }

        .info-header{ background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); padding: 22px; margin-bottom: 20px; position: relative; overflow: hidden; }
        .info-header::before{ content:""; position:absolute; top:-40px; right:-40px; width:150px; height:150px; border-radius:30px; opacity:.15; transform: rotate(15deg); background: var(--accent); }

        .info-title{ display:flex; align-items:center; gap:12px; margin:0 0 8px; color: var(--primary); font-weight: 950; font-size: 1.6rem; }
        .info-subtitle{ margin:0; color: var(--muted); font-weight: 700; font-size: 0.95rem; }

        .info-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
        .info-item{ background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 14px; box-shadow: var(--shadow-sm); transition: 0.2s; }
        .info-item:hover{ border-color: var(--accent); }
        .info-label{ display:block; font-size:.8rem; color: var(--muted); font-weight: 800; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
        .info-value{ font-size: 1.05rem; font-weight: 900; color: var(--text); }

        .section-title{ margin: 25px 0 15px; color: #0f172a; font-weight: 950; font-size: 1.2rem; display:flex; align-items:center; gap:12px; }
        .section-title::before{ content:""; width: 6px; height: 24px; border-radius: 999px; background: var(--accent); }

        .table-wrap{ overflow-x:auto; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        table{ width:100%; border-collapse: collapse; background: var(--card); }
        thead th{ background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; padding: 14px; text-align:left; font-size: .9rem; font-weight: 900; }
        tbody td{ padding: 14px; color: var(--text); border-bottom: 1px solid var(--border); background: #fff; font-weight: 700; }
        tbody tr:hover td{ background: rgba(255,215,0,0.05); }

        .status-badge{ display:inline-flex; padding:5px 12px; border-radius: 999px; font-size: .8rem; font-weight: 900; background: rgba(0,66,37,0.08); color: var(--primary); border: 1px solid rgba(0,66,37,0.1); }

        .footer-actions{ display:flex; justify-content:flex-end; margin-top: 20px; }
        .btn-back{ display:inline-flex; align-items:center; gap:8px; padding: 10px 20px; border-radius: 999px; font-weight: 900; text-decoration:none; background: rgba(255,215,0,.15); color: var(--primary); border: 1px solid rgba(255,215,0,.3); transition: 0.2s; }
        .btn-back:hover{ transform: translateX(-3px); background: rgba(255,215,0,.25); }

        @media (max-width: 900px){
            .sidebar { display: none; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">UKM PAGARNUSA UNUSA</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard.index') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li>
                <details open>
                    <summary class="active"><i class="fas fa-users-cog"></i> Master Data Pengguna</summary>
                    <ul class="sidebar-dropdown">
                        <li><a href="{{ route('member.index') }}" class="active">Manajemen Anggota</a></li>
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
                <details>
                    <summary><i class="fas fa-file-alt"></i> Laporan</summary>
                    <ul class="sidebar-dropdown">
                        <li><a href="{{ route('laporan.absensi') }}">Absensi</a></li>
                        <li><a href="{{ route('laporan.kas') }}">Kas</a></li>
                        <li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li>
                    </ul>
                </details>
            </li>
        </ul>
    </div>

    <div class="content-area">
        <div class="header-top-nav">
            <h1>Profil Anggota</h1>
            <div style="font-weight: 800; color: var(--primary);"><i class="fas fa-user-circle"></i> View Profile</div>
        </div>

        <div class="main-container">
            <div class="profile-card">
                <div class="info-header">
                    <h2 class="info-title"><i class="fas fa-id-card"></i> {{ $member->name }}</h2>
                    <p class="info-subtitle">Data lengkap anggota dan ringkasan aktivitas latihan terakhir.</p>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nama Lengkap</span>
                            <div class="info-value">{{ $member->name }}</div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIM / NIA</span>
                            <div class="info-value">{{ $member->member_id ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Program Studi</span>
                            <div class="info-value">{{ $member->study_program ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nomor Telepon</span>
                            <div class="info-value">{{ $member->phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <h3 class="section-title"><i class="fas fa-history"></i> Riwayat Kehadiran Terakhir</h3>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Latihan</th>
                                <th>Materi Latihan</th>
                                <th>Pelatih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentAttendances as $attendance)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($attendance->activity->date)->translatedFormat('d M Y') }}</td>
                                    <td>{{ $attendance->activity->material }}</td>
                                    <td>{{ $attendance->activity->trainer->name ?? 'N/A' }}</td>
                                    <td><span class="status-badge">{{ ucfirst($attendance->status) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 20px; color: var(--muted);">Belum ada riwayat kehadiran tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="footer-actions">
                    <a href="{{ route('member.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

</body>
</html>