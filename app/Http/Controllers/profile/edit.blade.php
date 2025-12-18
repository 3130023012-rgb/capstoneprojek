@php
    // Variabel $user dan $member diasumsikan dikirim dari ProfileController
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - UKM PAGARNUSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* [SALIN SEMUA CSS LENGKAP DARI DASHBOARD.BLADE.PHP DI SINI] */
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-dark: #343a40; --color-light: #f8f9fa; --color-white: #fff; --color-success: #28a745; 
            --color-primary: #007bff; --color-danger: #dc3545; --color-warning: #ffc107; --color-success-text: #155724;
            --font: "Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }
        *{ box-sizing:border-box; }
        body{ font-family: var(--font); margin: 0; min-height: 100vh; display: flex; color: var(--text); background: radial-gradient(900px 420px at 12% 0%, rgba(0,66,37,.10), transparent 60%), radial-gradient(700px 360px at 95% 10%, rgba(255,215,0,.14), transparent 60%), var(--bg); }
        .sidebar{ width: 240px; background: var(--card); box-shadow: 2px 0 12px rgba(0,0,0,.04); border-right: 1px solid var(--border); }
        .sidebar-header{ padding: 18px 16px; font-size: 1.15em; font-weight: 900; color: #fff; background: linear-gradient(135deg, var(--primary), var(--primary-2)); border-bottom: 1px solid rgba(0,0,0,.06); position: relative; letter-spacing: .2px; }
        .sidebar-header::after{ content:""; position:absolute; left:16px; right:16px; bottom:10px; height:3px; border-radius:999px; background: linear-gradient(90deg, transparent, rgba(255,215,0,.95), transparent); opacity:.95; }
        .sidebar-menu{ list-style:none; padding: 10px 0; margin:0; }
        .sidebar-menu summary::-webkit-details-marker { display:none; }
        .sidebar-menu summary{ list-style:none; }
        .sidebar-menu summary{ display:flex; justify-content:space-between; align-items:center; padding:12px 16px; font-size:15px; color:#0f172a; cursor:pointer; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu summary:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu summary::after{ content:'▼'; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
        .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }
        .sidebar-menu > li > a{ padding:12px 16px; text-decoration:none; font-size:15px; color:#0f172a; display:block; border-left:4px solid transparent; transition:.15s ease; font-weight:700; }
        .sidebar-menu > li > a:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu a.active, .sidebar-menu summary.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; margin:2px 12px 2px 0; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }
        .content{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.25em; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 10px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); transition: .15s ease; color: var(--primary); font-weight: 900; }
        .profile-access:hover{ background: rgba(255,215,0,.18); border-color: rgba(255,215,0,.35); transform: translateY(-1px); }
        .profile-name{ font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; box-shadow: 0 10px 18px rgba(0,66,37,.18); font-size: 1.05em; }
        .main-content-area{ padding: 26px 26px 20px; flex-grow: 1; min-width: 0; }
        .section-title{ color: #0f172a; font-size: 1.55em; font-weight: 950; margin: 12px 0 18px; text-align:center; letter-spacing:.15px; }
        .section-box{ background: var(--card); border:1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-md); padding:26px; margin-bottom:18px; }
        
        /* STYLE BARU UNTUK FORM PROFIL */
        .profile-form-group { margin-bottom: 18px; display: flex; flex-direction: column; max-width: 400px; }
        .profile-form-group label { font-weight: 700; margin-bottom: 6px; color: var(--primary); }
        .profile-form-group input[type="text"], .profile-form-group input[type="email"], .profile-form-group input[type="password"] { padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); font-family: var(--font); font-weight: 600; outline: none; transition: box-shadow 0.15s ease; }
        .profile-form-group input:focus { border-color: rgba(0,66,37,.45); box-shadow: 0 0 0 3px rgba(255,215,0,.22); }
        .form-separator { margin: 30px 0; border: 0; border-top: 1px solid var(--border); }
        
        /* ALERT STYLE */
        .alert{ padding:12px 14px; border-radius:12px; margin-bottom:16px; border:1px solid var(--border); box-shadow: var(--shadow-sm); font-weight:700; }
        .alert.success{ background: rgba(0,66,37,.08); color: var(--primary); border-color: rgba(0,66,37,.18); }
        .alert.danger{ background: rgba(220,53,69,.12); color:#991b1b; border-color: rgba(220,53,69,.20); }
        
        /* BUTTON STYLE */
        .btn-primary { 
            display: inline-flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 999px; 
            font-weight: 950; letter-spacing: .2px; text-decoration: none; box-shadow: 0 14px 24px rgba(0,0,0,.08); 
            transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
            background: var(--primary); color: #fff; box-shadow: 0 14px 24px rgba(0,66,37,.18); border: none;
            cursor: pointer;
        }
        .btn-primary:hover{ transform: translateY(-1px); background: var(--primary-2); }

        /* ✅ STYLE BARU: Tombol Kembali */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            margin-bottom: 20px;
            border-radius: 12px;
            background: var(--card);
            color: var(--muted);
            border: 1px solid var(--border);
            text-decoration: none;
            font-weight: 700;
            transition: background 0.15s ease;
            box-shadow: var(--shadow-sm);
        }
        .btn-back:hover {
            background: #f0f0f0;
            color: var(--text);
        }
        .btn-back span {
            font-size: 1.2em; /* Ikon panah */
        }
        
        /* FOOTER STYLE */
        .footer{ margin-top:auto; width:100%; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 14px; box-shadow: var(--shadow-sm); color: var(--muted); display:flex; justify-content:center; gap:10px; flex-wrap:wrap; }
        .footer span{ padding: 6px 10px; border-radius: 999px; background: rgba(0,66,37,.06); border: 1px solid rgba(0,66,37,.10); font-weight: 800; }

        /* responsive */
        @media (max-width: 980px){ .content{ padding:22px; } .sidebar{ width:220px; } }
        @media (max-width: 720px){ body{ display:block; } .sidebar{ width:100%; } .content{ min-height:auto; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">Admin UKM PAGARNUSA</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard.index') }}" class="active">Dashboard</a></li>
            <li><details><summary>Master Data Pengguna</summary><ul class="sidebar-dropdown"><li><a href="{{ route('member.index') }}">Manajemen Anggota</a></li><li><a href="{{ route('konten.index') }}">Manajemen Konten</a></li></ul></details></li>
            <li><a href="{{ route('absensi.index') }}">Absensi</a></li>
            <li><details><summary>Transaksi</summary><ul class="sidebar-dropdown"><li><a href="{{ route('kas.index') }}">Kas</a></li><li><a href="{{ route('kegiatan.index') }}">Kegiatan</a></li></ul></details></li>
            <li><details><summary>Laporan</summary><ul class="sidebar-dropdown"><li><a href="{{ route('laporan.absensi') }}">Absensi</a></li><li><a href="{{ route('laporan.kas') }}">Kas</a></li><li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li></ul></details></li>
            <li style="margin-top: 20px; border-top: 1px solid #eee;">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger); font-weight: bold;">Logout</a>
            </li>
        </ul>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="content">
        <div class="header-top">
            <h1>Profil Admin</h1>
            <a href="{{ route('profile.edit') }}" class="profile-access active">
                <span class="profile-name">{{ $user->name ?? 'Admin' }}</span>
                <span class="profile-icon-circle">👤</span>
            </a>
        </div>

        <div class="main-content-area">
            
            {{-- ✅ TOMBOL KEMBALI KE DASHBOARD --}}
            <a href="{{ route('dashboard.index') }}" class="btn-back">
                <span>&larr;</span> Kembali ke Dashboard
            </a>
            
            <h2 class="section-title">Edit Profil Saya</h2>

            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert danger">{{ session('error') }}</div>
            @endif

            <div class="section-box" style="max-width: 600px; margin: 0 auto 30px;">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <h3 style="font-size:1.5em; margin-top:0; color:var(--primary);">Informasi Akun Login</h3>
                    
                    <div class="profile-form-group">
                        <label for="name">Nama Lengkap (Akun):</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <span style="color:var(--color-danger); font-size:0.9em;">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="profile-form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <span style="color:var(--color-danger); font-size:0.9em;">{{ $message }}</span> @enderror
                    </div>

                    <hr class="form-separator">
                    
                    <h3 style="font-size:1.5em; color:var(--primary);">Biodata Anggota</h3>
                    
                    {{-- Pastikan $member ada sebelum mencoba mengakses propertinya --}}
                    @if ($member)
                        <div class="profile-form-group">
                            <label for="study_program">Program Studi:</label>
                            <input type="text" id="study_program" name="study_program" value="{{ old('study_program', $member->study_program ?? '') }}">
                            @error('study_program') <span style="color:var(--color-danger); font-size:0.9em;">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="profile-form-group">
                            <label for="phone_number">Nomor Telepon:</label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $member->phone_number ?? '') }}">
                            @error('phone_number') <span style="color:var(--color-danger); font-size:0.9em;">{{ $message }}</span> @enderror
                        </div>
                        
                    @else
                        <div class="alert danger">
                            Biodata Anggota belum terhubung. Harap hubungkan akun Admin ini ke data di tabel members.
                        </div>
                    @endif

                    <button type="submit" class="btn-primary" style="margin-top: 20px;">
                        Simpan Perubahan Profil
                    </button>
                </form>
            </div>
            
        </div>

        <div class="footer">
            <span>&copy; {{ date('Y') }} UKM PAGARNUSA</span>
            <span>All rights reserved</span>
        </div>
    </div>
</body>
</html>