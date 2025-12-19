@php
    use Carbon\Carbon;
    $filterDate = request('filter_month', Carbon::now()->format('Y-m'));
    $titleDate = Carbon::createFromFormat('Y-m', $filterDate);
    
    $totalIncome = $totalIncome ?? 0;
    $totalExpense = $totalExpense ?? 0;
    $saldoStart = $saldoStart ?? 0;
    $currentBalance = $saldoStart + $totalIncome - $totalExpense;
    $transactions = $transactions ?? collect();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas - UKM PAGARNUSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 
    
    <style>
        :root{
            --primary:#004225; --primary-2:#0a5a35; --accent:#FFD700;
            --bg:#f6faf7; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e5e7eb;
            --shadow-sm:0 2px 10px rgba(0,0,0,.06); --shadow-md:0 14px 30px rgba(0,0,0,.10); --radius:16px;
            --font: "Plus Jakarta Sans", sans-serif;
            --color-success: #28a745; --color-danger: #dc3545;
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
        .sidebar-dropdown{ list-style:none; padding:6px 0 10px 26px; margin:0; background:#fbfdfc; border-top:1px solid rgba(0,0,0,.03); border-bottom:1px solid rgba(0,0,0,.03); }
        .sidebar-dropdown a{ display:block; padding:8px 12px; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }

        /* --- CONTENT WRAPPER --- */
        .content{ flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
        .header-top h1{ margin:0; font-size: 1.4rem; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
        .profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
        .profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#fff; font-size: 1.05em; }

        .main-content-area{ padding: 30px 26px; }

        /* --- REPORT HEADER --- */
        .report-header { text-align: center; margin-bottom: 30px; }
        .report-title { color: var(--primary); font-weight: 950; margin: 0 auto 10px; display: inline-block; border-bottom: 3px solid var(--accent); padding-bottom: 5px; line-height: 1.4; font-size: 1.8em; }
        .report-period { color: var(--muted); font-weight: 700; font-size: 1.1em; }

        /* --- METRICS GRID --- */
        .metrics-grid{ display:grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .metric-card{ padding: 20px; border-radius: 20px; background: var(--card); border: 1px solid var(--border); box-shadow: 0 8px 18px rgba(0,0,0,.05); position: relative; overflow: hidden; transition: 0.3s; }
        .metric-card small{ color: var(--muted); font-weight: 800; display: block; margin-bottom: 8px; text-transform: uppercase; font-size: 0.8em; letter-spacing: 0.5px; }
        .metric-card p{ font-size: 1.45em; font-weight: 950; margin: 0; color: var(--primary); }
        .metric-card i { position: absolute; right: 20px; top: 20px; font-size: 1.6em; opacity: 0.1; color: var(--primary); }

        /* --- REPORT CARD & TABLE --- */
        .report-card{ background: var(--card); border-radius: var(--radius); border: 1px solid var(--border); padding: 24px; box-shadow: var(--shadow-md); }
        .filter-row{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        
        .table-responsive { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
        table{ width: 100%; border-collapse: collapse; background: #fff; }
        th{ text-align: left; padding: 15px; background: #f8fafc; color: var(--primary); font-weight: 900; border-bottom: 2px solid var(--border); font-size: 0.9em; }
        td{ padding: 15px; border-bottom: 1px solid #f1f5f9; font-weight: 700; font-size: 0.95em; }
        
        .badge-in{ color: #16a34a; background: #f0fdf4; padding: 5px 12px; border-radius: 99px; font-weight: 800; font-size: 0.75em; border: 1px solid rgba(22, 163, 74, 0.1); }
        .badge-out{ color: #dc3545; background: #fef2f2; padding: 5px 12px; border-radius: 99px; font-weight: 800; font-size: 0.75em; border: 1px solid rgba(220, 53, 69, 0.1); }

        .btn-print{ background: var(--accent); color: var(--primary); text-decoration: none; padding: 10px 22px; border-radius: 999px; font-weight: 900; display: inline-flex; align-items:center; gap: 8px; box-shadow: 0 10px 20px rgba(255,215,0,0.2); transition: 0.2s; border: none; cursor: pointer; }
        .btn-print:hover{ transform: translateY(-2px); filter: brightness(1.05); }

        @media print{ 
            .sidebar, .header-top, .filter-row, .btn-print{ display: none !important; } 
            body{ background: #fff; } 
            .main-content-area { padding: 0; }
            .report-card{ box-shadow: none; border: none; padding: 0; } 
            .report-title { font-size: 20pt; }
            table { width: 100% !important; border: 1px solid #333 !important; }
            th, td { border: 1px solid #333 !important; color: #000 !important; }
            th { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; }
            .metric-card { border: 1px solid #333; }
        }
        @media (max-width: 900px){ .sidebar { display: none; } .metrics-grid { grid-template-columns: 1fr; } }
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
                    <li><a href="{{ route('laporan.absensi') }}" >Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}" class="active">Kas</a></li>
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
            <h1><i class="fas fa-file-invoice-dollar" style="margin-right: 10px;"></i> Laporan Keuangan</h1>
            <div class="profile-access">
                Admin <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
            </div>
        </div>

        <div class="main-content-area">
            <div class="report-header">
                <h2 class="report-title">
                    REKAPITULASI TRANSAKSI KAS <br>
                    UKM PAGAR NUSA UNUSA
                </h2>
                <p class="report-period">Periode Laporan: {{ $titleDate->translatedFormat('F Y') }}</p>
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <i class="fas fa-file-download"></i>
                    <small>Total Pemasukan</small>
                    <p style="color: #16a34a;">+Rp {{ number_format($totalIncome,0,',','.') }}</p>
                </div>
                <div class="metric-card">
                    <i class="fas fa-file-upload"></i>
                    <small>Total Pengeluaran</small>
                    <p style="color: #dc3545;">-Rp {{ number_format($totalExpense,0,',','.') }}</p>
                </div>
                <div class="metric-card">
                    <i class="fas fa-wallet"></i>
                    <small>Saldo Akhir Periode</small>
                    <p>Rp {{ number_format($currentBalance,0,',','.') }}</p>
                </div>
            </div>

            <div class="report-card">
                <div class="filter-row">
                    <form method="GET" action="{{ route('laporan.kas') }}" style="display: flex; gap: 10px;">
                        <select name="filter_month" onchange="this.form.submit()" style="padding:10px 15px; border-radius:12px; border:1px solid var(--border); font-weight:800; outline:none; cursor:pointer;">
                            @for ($i = 0; $i < 12; $i++)
                                @php $date = Carbon::now()->subMonths($i); @endphp
                                <option value="{{ $date->format('Y-m') }}" {{ $filterDate == $date->format('Y-m') ? 'selected' : '' }}>
                                    {{ $date->translatedFormat('F Y') }}
                                </option>
                            @endfor
                        </select>
                        <button type="submit" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer;"><i class="fas fa-filter"></i> Filter</button>
                    </form>
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print"></i> Cetak Laporan (PDF)
                    </button>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan Transaksi</th>
                                <th style="text-align: center;">Tipe</th>
                                <th style="text-align: right;">Nominal (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                            <tr>
                                <td>{{ Carbon::parse($t->date)->translatedFormat('d M Y') }}</td>
                                <td style="color: var(--primary);">{{ $t->description }}</td>
                                <td style="text-align: center;">
                                    <span class="{{ $t->type == 'in' ? 'badge-in' : 'badge-out' }}">
                                        {{ $t->type == 'in' ? 'MASUK' : 'KELUAR' }}
                                    </span>
                                </td>
                                <td style="text-align: right; font-family: 'Courier New', Courier, monospace;">
                                    Rp {{ number_format($t->amount,0,',','.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 40px; color: var(--muted);">
                                    <i class="fas fa-search-dollar fa-2x" style="display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                                    Tidak ada riwayat transaksi pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>