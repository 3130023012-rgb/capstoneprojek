@php
    use Carbon\Carbon;
    $members = $members ?? collect(); // Pastikan variabel members tersedia
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Kehadiran - UKM PAGARNUSA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225;
            --primary-2:#0a5a35;
            --accent:#FFD700;
            --bg:#f6faf7;
            --card:#ffffff;
            --text:#0f172a;
            --muted:#64748b;
            --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06);
            --shadow-md:0 14px 30px rgba(0,0,0,.10);
            --radius:16px;
            --color-danger:#dc3545; 
            --color-success: #28a745;
            --color-white: #fff;
            --font: "Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        *{ box-sizing:border-box; }

        body{
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
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* --- CONTENT WRAPPER --- */
        .content-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); font-size: 1.05em; }

        .main-content-area { padding: 30px 26px 20px; flex-grow: 1; }
        .page-header-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow-md); margin-bottom: 25px; position: relative; overflow: hidden; }
        .page-header-box::before{ content:""; position:absolute; top:-60px; right:-60px; width:180px; height:180px; border-radius:28px; opacity:.18; transform: rotate(12deg); background: radial-gradient(circle at 30% 30%, var(--accent), transparent 60%); }
        .page-header-box h2 { margin: 0; font-size: 1.8em; font-weight: 950; color: var(--primary); }
        
        .section-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); padding: 25px; margin-bottom: 25px; }
        .section-box h3 { margin-top: 0; margin-bottom: 20px; font-weight: 900; color: var(--primary); border-bottom: 2px solid var(--accent); display: inline-block; padding-bottom: 5px; }

        /* --- FORMS --- */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 800; color: var(--text); font-size: 0.95em; }
        input[type="date"], input[type="text"] {
            width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--border);
            font-family: var(--font); font-weight: 700; background:#fff; outline: none; transition: 0.2s;
        }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0,66,37,0.1); }

        /* --- TABLE --- */
        .table-responsive { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        table thead th { background: #f8fafc; color: var(--primary); padding: 15px; font-weight: 800; text-align: center; border-bottom: 2px solid var(--border); }
        table tbody td { padding: 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; text-align: center; font-weight: 600; }
        table tbody tr:hover { background: rgba(0,66,37,0.02); }

        /* --- BUTTONS --- */
        .btn { padding: 12px 25px; border-radius: 999px; border: none; cursor: pointer; font-weight: 900; font-family: var(--font); transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #fff; box-shadow: 0 10px 20px rgba(0,66,37,0.15); }
        .btn-primary:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .btn-primary:disabled { background: #ccc; cursor: not-allowed; transform: none; box-shadow: none; }
        
        .btn-link { color: var(--muted); font-weight: 800; text-decoration: none; }
        .btn-link:hover { color: var(--primary); text-decoration: underline; }

        .btn-tambah-anggota { background: rgba(255,215,0,0.15); color: #856404; border: 1px solid rgba(255,215,0,0.3); padding: 8px 16px; border-radius: 10px; font-size: 0.85em; font-weight: 800; }

        /* --- ALERTS --- */
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; border: 1px solid transparent; }
        .alert.success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .alert.danger { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

        .footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); }

        @media (max-width: 900px){ .sidebar { display: none; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">UKM PAGARNUSA</div>
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
            <h1><i class="fas fa-user-check" style="margin-right: 10px;"></i> Pencatatan Absensi</h1>
            <a href="#" class="profile-access">
                <span class="profile-name">Admin</span>
                <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </a>
        </div>

        <div class="main-content-area">
            <div class="page-header-box">
                <h2>Catat Kehadiran Kegiatan Baru</h2>
                <p>Formulir untuk mendokumentasikan materi latihan dan kehadiran anggota secara berkala.</p>
            </div>

            @if (session('success')) <div class="alert success">{{ session('success') }}</div> @endif
            @if ($errors->any())
                <div class="alert danger">
                    <i class="fas fa-exclamation-triangle"></i> Mohon periksa kesalahan berikut:
                    <ul style="margin: 10px 0 0; padding-left: 20px; font-weight: 500;">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                
                <div class="section-box">
                    <h3>Detail Kegiatan</h3>
                    <div class="form-group">
                        <label for="date">Tanggal Kegiatan</label>
                        <input type="date" name="date" id="date" value="{{ old('date', Carbon::now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="material">Materi / Nama Kegiatan</label>
                        <input type="text" name="material" id="material" value="{{ old('material') }}" placeholder="Contoh: Teknik Dasar Jurus 1" required>
                    </div>
                    <div class="form-group">
                        <label for="trainer_name">Nama Pelatih / Penanggung Jawab</label>
                        <input type="text" name="trainer_name" id="trainer_name" value="{{ old('trainer_name') }}" placeholder="Masukkan nama pelatih" required>
                    </div>
                </div>

                <div class="section-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3>Daftar Kehadiran Anggota</h3>
                        <a href="{{ route('member.create') }}" class="btn-tambah-anggota"><i class="fas fa-user-plus"></i> Tambah Anggota Baru</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th style="text-align: left; width: 35%;">Nama Anggota</th>
                                    <th>NIM</th>
                                    <th style="color: var(--color-success);">Hadir</th>
                                    <th style="color: var(--color-danger);">Absen</th>
                                    <th style="color: #856404;">Sakit</th>
                                    <th style="color: #1e40af;">Izin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($members as $member)
                                    <tr>
                                        <td style="text-align: left; color: var(--primary);">{{ $member->name }}</td>
                                        <td><span style="font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;">{{ $member->member_id ?? '-' }}</span></td>
                                        <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="present" required {{ old("member_statuses.{$member->id}") == 'present' ? 'checked' : '' }}></td>
                                        <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="absent" required {{ old("member_statuses.{$member->id}") == 'absent' ? 'checked' : '' }}></td>
                                        <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="sick_leave" required {{ old("member_statuses.{$member->id}") == 'sick_leave' ? 'checked' : '' }}></td>
                                        <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="permission" required {{ old("member_statuses.{$member->id}") == 'permission' ? 'checked' : '' }}></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 40px; color: var(--muted);">
                                            Belum ada data anggota. <a href="{{ route('member.create' ) }}" class="btn-link">Klik di sini untuk tambah anggota.</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                        <a href="{{ route('absensi.index') }}" class="btn-link" style="align-self: center;">Batal</a>
                        <button type="submit" class="btn btn-primary" {{ $members->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i> Simpan Data Kehadiran
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="footer">
            <span>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA</span>
            <span><i class="fas fa-shield-alt"></i> Wira Laga Santri</span>
        </div>
    </div>
</body>
</html>