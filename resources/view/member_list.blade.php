@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Pengguna - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-white: #fff; --color-danger: #dc3545; --color-success: #28a745;
            --font: "Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        *{ box-sizing:border-box; }

        body{
            font-family: var(--font); margin: 0; min-height: 100vh; display: flex; color: var(--text);
            background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%),
            radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg);
        }

        /* SIDEBAR */
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: #fff; background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        
        .sidebar-menu{ list-style:none; padding:10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); }
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

        /* CONTENT AREA */
        .content{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.5em; font-weight: 950; color: var(--primary); letter-spacing:.2px; }

        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); transition: .15s ease; color: var(--primary); font-weight: 900; }
        .profile-access:hover{ background: rgba(255,215,0,.18); border-color: rgba(255,215,0,.35); transform: translateY(-1px); }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); font-size: 1.05em; }

        .main-content-area{ padding: 30px 26px 20px; flex-grow: 1; }
        .section-title{ color: #0f172a; font-size: 1.8em; font-weight: 950; margin: 0 0 24px; border-bottom: 2px solid var(--accent); padding-bottom: 8px; display:inline-block; }

        /* PANEL & TABLE */
        .panel{ background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-md); padding: 20px; }
        
        .top-actions{ display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; }
        .search-box{ display: flex; gap: 10px; flex-grow: 1; max-width: 500px; }
        .search-box input{ flex: 1; padding: 10px 15px; border-radius: 12px; border: 1px solid var(--border); font-weight: 700; font-family: var(--font); outline: none; }
        .search-box input:focus{ border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,66,37,0.1); }
        .btn-search{ background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; }
        .btn-search:hover{ background: var(--primary-2); }

        .table-container{ overflow-x:auto; border-radius: 12px; border: 1px solid var(--border); }
        table{ width:100%; border-collapse: collapse; background:#fff; }
        thead th{ background: #f8fafc; color: var(--primary); padding: 15px; text-align: left; font-weight: 800; border-bottom: 2px solid var(--border); font-size: 0.9em; }
        tbody td{ padding: 15px; border-bottom: 1px solid #f1f5f9; font-weight: 600; font-size: 0.95em; vertical-align: middle; }
        tbody tr:hover{ background: rgba(0,66,37,0.02); }

        /* BADGES */
        .role-badge{ padding: 5px 12px; border-radius: 999px; font-size: 0.8em; font-weight: 800; display: inline-block; }
        .role-badge.pengurus{ background: rgba(22,163,74,.1); color: #16a34a; }
        .role-badge.pembina{ background: rgba(255,215,0,.15); color: #b59400; }
        .role-badge.anggota{ background: rgba(59,130,246,.1); color: #2563eb; }
        .role-badge.none{ background: rgba(220,53,69,.1); color: #dc3545; }

        .status-tag{ background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 0.9em; color: var(--primary); }

        /* ACTIONS */
        .action-btn{ text-decoration: none; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.85em; transition: 0.2s; display: inline-block; margin: 2px; }
        .btn-view{ background: rgba(0,66,37,0.05); color: var(--primary); border: 1px solid rgba(0,66,37,0.1); }
        .btn-edit{ background: rgba(255,215,0,0.1); color: #856404; border: 1px solid rgba(255,215,0,0.2); }
        .btn-delete{ background: rgba(220,53,69,0.05); color: #dc3545; border: 1px solid rgba(220,53,69,0.1); }
        .btn-create-acc{ background: var(--primary); color: white; padding: 8px 14px; border-radius: 10px; font-size: 0.8em; }

        .footer{ background: var(--card); padding: 14px 26px; border-top: 1px solid var(--border); text-align: center; font-size: .9em; color: var(--muted); display:flex; justify-content: space-between; align-items: center; }

        @media (max-width: 900px){
            .sidebar { display: none; }
            .metrics-grid{ grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">UKM PAGARNUSA UNUSA</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard.index') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><details open>
                <summary class="active"><i class="fas fa-users-cog"></i> Master Data Pengguna</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('member.index') }}" class="active">Manajemen Anggota</a></li>
                    <li><a href="{{ route('konten.index') }}">Manajemen Konten</a></li>
                </ul>
            </details></li>
            <li><a href="{{ route('absensi.index') }}"><i class="fas fa-clipboard-check"></i> Absensi</a></li>
            <li><details><summary><i class="fas fa-cash-register"></i> Transaksi</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('kas.index') }}">Kas</a></li>
                    <li><a href="{{ route('kegiatan.index') }}">Kegiatan</a></li>
                </ul>
            </details></li>
            <li><details><summary><i class="fas fa-file-alt"></i> Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}">Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}">Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li>
                </ul>
            </details></li>
            <li style="margin-top: 20px; border-top: 1px solid #eee;">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="content">
        <div class="header-top">
            <h1><i class="fas fa-users" style="margin-right: 10px;"></i> Manajemen Anggota</h1>
            <a href="{{ route('profile.edit') }}" class="profile-access">
                <span class="profile-name" style="font-weight: 900;">Admin</span>
                <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </a>
        </div>

        <div class="main-content-area">
            <h2 class="section-title">Daftar Seluruh Anggota</h2>

            @if(session('success'))
                <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="panel">
                <div class="top-actions">
                    <form action="{{ route('member.index') }}" method="GET" class="search-box">
                        <input type="text" name="q" placeholder="Cari Nama, NIM, atau Prodi..." value="{{ $searchTerm ?? '' }}">
                        <button type="submit" class="btn-search">Cari</button>
                        @if($searchTerm ?? false)
                            <a href="{{ route('member.index') }}" style="color: var(--color-danger); font-weight: 800; text-decoration: none; align-self: center; margin-left: 10px;">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>NIM / NIA</th>
                                <th>Program Studi</th>
                                <th>Peran Akun</th>
                                <th>Username</th>
                                <th style="text-align:center;">Aksi Data</th>
                                <th style="text-align:center;">Status Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td><span class="status-tag">{{ $member->member_id ?? '-' }}</span></td>
                                    <td>{{ $member->study_program ?? '-' }}</td>
                                    <td>
                                        @if ($member->user_id && $member->user->role)
                                            <span class="role-badge {{ strtolower($member->user->role->name) }}">
                                                {{ ucfirst($member->user->role->name) }}
                                            </span>
                                        @else
                                            <span class="role-badge none">No Account</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($member->user_id)
                                            <span class="status-tag">{{ $member->user->username }}</span>
                                        @else
                                            <span style="color: var(--muted); font-style: italic;">Belum ada</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('member.profile', $member) }}" class="action-btn btn-view" title="Lihat"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('member.edit', $member) }}" class="action-btn btn-edit">Edit</a>
                                        <form action="{{ route('member.destroy', $member) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn btn-delete" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                        </form>
                                    </td>
                                    <td style="text-align:center;">
                                        @if(!$member->user_id)
                                            <a href="{{ route('member.user.create', ['member_id'=>$member->id]) }}" class="action-btn btn-create-acc">
                                                <i class="fas fa-plus-circle"></i> Buat Akun
                                            </a>
                                        @else
                                            <span style="color: var(--color-success); font-weight: 900;"><i class="fas fa-check-circle"></i> AKTIF</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center; padding: 30px; color: var(--muted);">Data tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="footer">
            <div><span><i class="fas fa-code"></i> Dibuat oleh UKM IT Team</span></div>
            <div><span>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA</span></div>
        </div>
    </div>
</body>
</html>