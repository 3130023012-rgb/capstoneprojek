<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Anggota - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --danger:#dc3545; --color-white: #fff;
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

        /* --- CONTENT AREA --- */
        .content{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.5em; font-weight: 950; color: var(--primary); }

        .main-content-area{ padding: 30px 26px; }

        /* --- FORM STYLES --- */
        .form-container{
            width:100%;
            max-width: 980px;
            background: rgba(255,255,255,0.78);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 22px;
            backdrop-filter: blur(6px);
        }

        .page-title{ text-align:center; margin: 6px 0 18px; }
        .page-title h2{ margin:0; font-size: 1.9rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .page-title .underline{ width: 220px; height: 4px; border-radius:999px; margin: 10px auto 0; background: linear-gradient(90deg, transparent, var(--accent), transparent); }

        .form-layout{
            display:flex; gap: 26px; align-items:flex-start; padding: 18px; border-radius: var(--radius);
            background: var(--card); border: 1px solid var(--border); box-shadow: var(--shadow-sm);
        }

        .header-side{ flex-basis: 38%; padding-top: 6px; }
        .header-side h3{ margin:0 0 6px; font-size: 1.35rem; font-weight: 950; color: var(--primary); }
        .header-side p{ margin:0; color: var(--muted); font-weight: 700; line-height:1.5; }

        .form-fields-side{ flex-basis: 62%; }

        .error-box{ border: 1px solid rgba(220,53,69,.35); background: rgba(220,53,69,.10); color: #991b1b; border-radius: 12px; padding: 12px 14px; margin-bottom: 14px; font-weight: 800; }
        .error-box ul{ margin: 8px 0 0 18px; font-weight: 700; }

        .form-group{ margin-bottom: 16px; }
        .form-group label{ display:block; margin-bottom: 8px; font-weight: 900; color: var(--text); font-size: .95rem; }
        .form-group input{
            width:100%; padding: 12px; border-radius: 12px; border: 1px solid var(--border); outline:none;
            font-weight: 700; background:#fff; transition: all .12s ease;
        }
        .form-group input:focus{ border-color: rgba(0,66,37,.45); box-shadow: 0 0 0 4px rgba(255,215,0,.22); }

        .error{ color: var(--danger); font-size: .88rem; margin-top: 6px; font-weight: 800; }

        .button-group{
            display:flex; justify-content:flex-end; gap: 10px; padding-top: 14px; margin-top: 16px;
            border-top: 1px solid rgba(0,0,0,.06);
        }

        .btn{
            padding: 10px 20px; border-radius: 999px; font-weight: 950; cursor:pointer;
            text-decoration:none; display:inline-flex; align-items:center; transition: all .12s ease;
        }
        .btn-batal{ background: rgba(255,215,0,.22); color: var(--primary); border: 1px solid rgba(255,215,0,.35); }
        .btn-simpan{ background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #fff; border: none; box-shadow: 0 10px 20px rgba(0,66,37,.15); }
        .btn:hover{ transform: translateY(-1px); filter: brightness(0.95); }

        @media (max-width: 900px){
            .sidebar { display: none; }
            .form-layout{ flex-direction: column; }
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

    <div class="content">
        <div class="header-top">
            <h1>Administrasi Anggota</h1>
            <div style="font-weight: 800; color: var(--primary);">Admin UKM</div>
        </div>

        <div class="main-content-area">
            <div class="form-container">
                <div class="page-title">
                    <h2>Tambah Data Anggota</h2>
                    <div class="underline"></div>
                </div>

                <form action="{{ route('member.store') }}" method="POST">
                    @csrf
                    <div class="form-layout">
                        <div class="header-side">
                            <h3>Form Anggota</h3>
                            <p>Silakan isi data anggota UKM PAGARNUSA dengan benar untuk keperluan database dan akses sistem.</p>
                        </div>

                        <div class="form-fields-side">
                            @if ($errors->any())
                                <div class="error-box">
                                    Terjadi Kesalahan:
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan Nama Lengkap" required>
                                @error('name') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="member_id">NIM (Nomor Induk Mahasiswa)</label>
                                <input type="text" name="member_id" id="member_id" value="{{ old('member_id') }}" placeholder="Masukkan NIM">
                                @error('member_id') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="study_program">Program Studi</label>
                                <input type="text" name="study_program" id="study_program" value="{{ old('study_program') }}" placeholder="Masukkan Program Studi">
                                @error('study_program') <div class="error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="button-group">
                        <a href="{{ route('absensi.create') }}" class="btn btn-batal">Batal</a>
                        <button type="submit" class="btn btn-simpan">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

</body>
</html>