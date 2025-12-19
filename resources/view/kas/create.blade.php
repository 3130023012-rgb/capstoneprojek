@php
    use Carbon\Carbon;
    
    // Inisialisasi variabel prefill dengan default yang aman
    $prefillData = $prefillData ?? [
        'user_id' => null, 
        'member_id' => null,
        'type' => 'in', 
        'description' => null, 
        'current_month' => Carbon::now()->translatedFormat('F Y')
    ];
    $memberName = $memberName ?? 'Data Anggota Tidak Ditemukan'; 
    $activities = $activities ?? collect(); 
    
    $defaultDate = old('date', date('Y-m-d'));
    $pageTitle = 'Pencatatan Iuran Anggota';
    
    $defaultMonthYearValue = old('iuran_month_year', $prefillData['default_month_year'] ?? Carbon::now()->format('Y-m'));
    
    $monthsList = [];
    $startMonth = Carbon::now()->startOfMonth();
    for ($i = 0; $i < 12; $i++) {
        $date = $startMonth->copy()->subMonths($i);
        $monthsList[$date->format('Y-m')] = $date->translatedFormat('F Y');
    }

    $selectedMonthDisplay = $monthsList[$defaultMonthYearValue] ?? $prefillData['current_month'];
    
    $defaultDescriptionValue = old('description', 
        $prefillData['description'] ?: ("Iuran Anggota {$memberName} Bulan {$selectedMonthDisplay}")
    );
    
    $isIuran = true; 
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - UKM PAGARNUSA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --color-danger:#dc3545; --color-success: #28a745; --color-white: #fff;
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
        .sidebar-menu{ list-style:none; padding:10px 0; margin:0; }
        .sidebar-menu i { margin-right: 10px; color: rgba(0,66,37,.6); }
        .sidebar-menu summary::-webkit-details-marker { display:none; }
        .sidebar-menu summary, .sidebar-menu > li > a { display:flex; align-items:center; padding:12px 16px; font-size:15px; color:#0f172a; cursor:pointer; border-left:4px solid transparent; text-decoration:none; transition:.15s ease; font-weight:700; }
        .sidebar-menu summary:hover, .sidebar-menu > li > a:hover{ background: rgba(0,66,37,.06); }
        .sidebar-menu summary::after{ content:'▼'; margin-left:auto; font-size:.7em; color: rgba(0,66,37,.75); transition: transform .2s ease; }
        .sidebar-menu details[open] > summary::after{ transform: rotate(180deg); }
        .sidebar-menu a.active, .sidebar-menu summary.active{ background: rgba(0,66,37,.10); border-left-color: var(--accent); color: var(--primary); font-weight: 900; }
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }
        .sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

        /* --- CONTENT --- */
        .content-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); font-size: 1.05em; }

        .main-content-area { padding: 30px 26px 20px; flex-grow: 1; }
        .page-header-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow-md); margin-bottom: 25px; position: relative; overflow: hidden; }
        .page-header-box::before{ content:""; position:absolute; top:-60px; right:-60px; width:180px; height:180px; border-radius:28px; opacity:.18; transform: rotate(12deg); background: radial-gradient(circle at 30% 30%, var(--accent), transparent 60%); }
        .page-header-box h2 { margin: 0; font-size: 1.8em; font-weight: 950; color: var(--primary); }

        /* --- FORM STYLES --- */
        .form-card { background: var(--card); padding: 30px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-md); max-width: 600px; margin: 0 auto; }
        .form-card h3 { color: var(--primary); margin-top: 0; margin-bottom: 20px; font-weight: 900; border-bottom: 2px solid var(--accent); display: inline-block; padding-bottom: 5px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 800; font-size: 0.9em; color: var(--text); }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 12px; background: #f8fafc; font-family: var(--font); font-weight: 600; outline: none; transition: 0.2s; }
        .form-group input:focus, .form-group select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0,66,37,0.1); background: #fff; }
        
        .info-static { padding: 12px 15px; background: #f1f5f9; border-radius: 12px; border: 1px solid var(--border); font-weight: 900; color: var(--primary); font-size: 1.1em; }
        
        .error { color: var(--color-danger); font-size: 0.85em; margin-top: 5px; font-weight: 700; display: block; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; }

        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn-submit { background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #fff; padding: 12px 25px; border: none; border-radius: 999px; cursor: pointer; font-weight: 900; transition: 0.2s; box-shadow: 0 10px 20px rgba(0,66,37,0.15); flex: 1; }
        .btn-submit:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .btn-cancel { background: #f1f5f9; color: #475569; padding: 12px 25px; border-radius: 999px; text-decoration: none; font-weight: 800; border: 1px solid var(--border); transition: 0.2s; text-align: center; }
        .btn-cancel:hover { background: #e2e8f0; }

        @media (max-width: 900px){ .sidebar { display: none; } }
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
            <li><details><summary><i class="fas fa-users-cog"></i> Master Data</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('member.index') }}">Manajemen Anggota</a></li>
                    <li><a href="{{ route('konten.index') }}">Manajemen Konten</a></li>
                </ul></details></li>
            <li><a href="{{ route('absensi.index') }}"><i class="fas fa-clipboard-check"></i> Absensi</a></li>
            <li><details open><summary class="active"><i class="fas fa-cash-register"></i> Transaksi</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('kas.index') }}" class="active">Kas</a></li> 
                    <li><a href="{{ route('kegiatan.index') }}">Kegiatan</a></li>
                </ul></details></li>
            <li><details><summary><i class="fas fa-file-alt"></i> Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}">Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}">Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}">Kegiatan</a></li>
                </ul></details></li>
            <li style="margin-top: 20px; border-top: 1px solid #eee;">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger); font-weight: bold;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <div class="content-wrapper">
        <div class="header-top">
            <h1><i class="fas fa-hand-holding-usd" style="margin-right: 10px;"></i> Transaksi Iuran</h1>
            <a href="{{ route('profile.edit') }}" class="profile-access">
                <span class="profile-name">Admin</span>
                <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </a>
        </div>

        <div class="main-content-area">
            <div class="page-header-box">
                <h2>{{ $pageTitle }}</h2>
                <p>Silakan lengkapi data pembayaran iuran kas anggota UKM PAGARNUSA.</p>
            </div>

            <div class="form-card">
                <h3><i class="fas fa-file-invoice-dollar"></i> Formulir Input Iuran</h3>

                @if ($errors->any())
                    <div class="alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Mohon periksa kembali input Anda:
                        <ul style="margin-top: 10px; margin-bottom: 0;">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('kas.store') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="user_id" value="{{ $prefillData['user_id'] }}">
                    <input type="hidden" name="member_id" value="{{ $prefillData['member_id'] }}"> 
                    <input type="hidden" name="type" value="in"> 
                    <input type="hidden" name="date" value="{{ $defaultDate }}">
                    <input type="hidden" name="activity_id" value=""> 

                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <div class="info-static">
                            <i class="fas fa-user" style="margin-right: 10px; opacity: 0.5;"></i> {{ $memberName }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="iuran_month_year">Bulan Iuran</label>
                        <select name="iuran_month_year" id="iuran_month_year" required>
                            @foreach ($monthsList as $value => $label)
                                <option value="{{ $value }}" {{ $value == $defaultMonthYearValue ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('iuran_month_year') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Nominal (Rp)</label>
                        <input type="number" name="amount" value="{{ old('amount') }}" placeholder="Contoh: 50000" required>
                        @error('amount') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Keterangan Deskripsi</label>
                        <input type="text" name="description" value="{{ $defaultDescriptionValue }}" placeholder="Contoh: Iuran bulan Desember">
                        @error('description') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Simpan Transaksi
                        </button>
                        <a href="{{ route('kas.index') }}" class="btn-cancel">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
