@php
    use Carbon\Carbon;
    
    // Variabel yang diharapkan dikirim dari KasController@index:
    $memberStatus = $memberStatus ?? collect(); 
    $totalKas = $totalKas ?? 0; 
    $saldo = $saldo ?? 0; 
    
    // Variabel Filter dari Controller
    $filterMonth = $filterMonth ?? Carbon::now()->format('Y-m');
    $bulanFilter = $bulanFilter ?? Carbon::now()->startOfMonth();
    
    // Generate list of months (Current month dan 11 bulan sebelumnya untuk filter)
    $monthsList = [];
    $startMonth = Carbon::now()->startOfMonth();
    for ($i = 0; $i < 12; $i++) {
        $date = $startMonth->copy()->subMonths($i);
        $monthsList[$date->format('Y-m')] = $date->translatedFormat('F Y');
    }
    
    // Fungsi helper format Rupiah
    if (!function_exists('formatRupiah')) {
        function formatRupiah($amount) {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Kas UKM PAGARNUSA</title>
    
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
*{ box-sizing: border-box; }
body { 
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
.sidebar-dropdown a{ display:block; padding:8px 12px; margin:2px 12px 2px 0; font-size:.92em; color:#0f172a; border-radius:12px; text-decoration:none; transition: background .15s ease; font-weight:700; }
.sidebar-dropdown a:hover{ background: rgba(255,215,0,.18); }

/* --- CONTENT --- */
.content-wrapper { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }
.header-top{ background: var(--card); padding: 16px 22px; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items:center; box-shadow: var(--shadow-sm); }
.header-top h1{ margin:0; font-size: 1.5em; font-weight: 950; color: var(--primary); letter-spacing:.2px; }
.profile-access{ display:inline-flex; align-items:center; gap:10px; text-decoration:none; padding:8px 12px; border-radius:999px; border:1px solid rgba(0,66,37,.12); background: rgba(0,66,37,.05); color: var(--primary); font-weight: 900; }
.profile-icon-circle{ width: 34px; height: 34px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:var(--color-white); font-size: 1.05em; }

.main-content-area { padding: 30px 26px 20px; flex-grow: 1; }
.page-header-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow-md); margin-bottom: 25px; position: relative; overflow: hidden; }
.page-header-box::before{ content:""; position:absolute; top:-60px; right:-60px; width:180px; height:180px; border-radius:28px; opacity:.18; transform: rotate(12deg); background: radial-gradient(circle at 30% 30%, var(--accent), transparent 60%); }
.page-header-box h2 { margin: 0; font-size: 1.8em; font-weight: 950; color: var(--primary); }

/* --- KAS CARDS --- */
.ringkasan-kas-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
.kas-card { background: var(--card); border: 1px solid rgba(0,66,37,0.15); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden; }
.kas-card small { display: block; color: var(--muted); font-weight: 800; letter-spacing: .5px; margin-bottom: 8px; text-transform: uppercase; font-size: 0.85em; }
.kas-card p { margin: 0; font-size: 1.8em; font-weight: 950; color: var(--primary); }

/* --- TABLE & FILTER --- */
.section-box { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); padding: 25px; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.section-header h3 { margin: 0; font-weight: 900; color: var(--primary); border-bottom: 2px solid var(--accent); padding-bottom: 5px; font-size: 1.25em; }

.filter-group select { padding: 10px 15px; border-radius: 12px; border: 1px solid var(--border); font-family: var(--font); font-weight: 800; color: var(--text); outline: none; }
.filter-group button { padding: 10px 20px; border-radius: 12px; border: none; background: var(--primary); color: #fff; font-weight: 800; cursor: pointer; transition: 0.2s; }

.table-responsive { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; background: #fff; }
thead th { background: #f8fafc; color: var(--primary); padding: 15px; font-weight: 800; text-align: left; border-bottom: 2px solid var(--border); }
tbody td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-weight: 600; font-size: 0.95em; vertical-align: middle; }
tbody tr:hover { background: rgba(0,66,37,0.02); }

/* --- BADGES --- */
.badge-status { display: inline-flex; padding: 5px 12px; border-radius: 999px; font-size: .75em; font-weight: 950; }
.type-in { background: #dcfce7; color: #166534; }
.type-out { background: #fee2e2; color: #991b1b; }
.btn-batal-aksi { background: #f1f5f9; color: #475569; padding: 6px 14px; border-radius: 8px; border: 1px solid var(--border); font-weight: 800; cursor: pointer; font-size: 0.85em; transition: 0.2s; }
.btn-batal-aksi:hover { background: #e2e8f0; }

.footer { padding: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-weight: 700; font-size: 0.9em; background: var(--card); }

@media (max-width: 900px) { .sidebar { display: none; } .ringkasan-kas-grid { grid-template-columns: 1fr; } }
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
            <details open>  
                <summary class="active"><i class="fas fa-cash-register"></i> Transaksi</summary> 
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('kas.index') }}" class="active">Kas</a></li> 
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
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger); font-weight: bold;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

<div class="content-wrapper">
    <div class="header-top">
        <h1><i class="fas fa-wallet" style="margin-right: 10px;"></i> Manajemen Kas</h1>
        <a href="{{ route('profile.edit') }}" class="profile-access">
            <span class="profile-name">Admin</span>
            <span class="profile-icon-circle"><i class="fas fa-user-shield"></i></span>
        </a>
    </div>

    <div class="main-content-area">
        <div class="page-header-box">
            <h2>Dashboard Administrasi Kas</h2>
            <p>Kelola iuran bulanan anggota dan pantau saldo kas UKM secara real-time.</p>
        </div>

        <div class="ringkasan-kas-grid">
            <div class="kas-card">
                <small>Total Pemasukan Kas</small>
                <p>{{ formatRupiah($totalKas ?? 0) }}</p>
            </div>
            <div class="kas-card">
                <small>Saldo Kas Terkini</small>
                <p>{{ formatRupiah($saldo ?? 0) }}</p>
            </div>
        </div>

        <div class="section-box">
            <div class="section-header">
                <h3><i class="fas fa-history"></i> Iuran Anggota: {{ $bulanFilter->translatedFormat('F Y') }}</h3>
                
                <form action="{{ route('kas.index') }}" method="GET" class="filter-group" style="display: flex; gap: 10px;">
                    <select name="filter_month" id="filter_month">
                        @foreach ($monthsList as $value => $label)
                            <option value="{{ $value }}" {{ $value == $filterMonth ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"><i class="fas fa-filter"></i> Filter</button>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 35%;">Nama Anggota</th>
                            <th style="width: 20%;">Status Iuran</th>
                            <th style="width: 20%;">Pembayaran Terakhir</th>
                            <th style="width: 20%; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberStatus as $key => $member)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td style="color: var(--primary); font-weight: 800;">{{ $member->name }}</td>
                            <td>
                                <span class="badge-status {{ $member->status == 'Sudah Bayar' ? 'type-in' : 'type-out' }}">
                                    <i class="fas {{ $member->status == 'Sudah Bayar' ? 'fa-check-circle' : 'fa-times-circle' }}"></i> 
                                    {{ $member->status }}
                                </span>
                            </td>
                            <td>
                                @if($member->last_payment_date)
                                    {{ Carbon::parse($member->last_payment_date)->translatedFormat('F Y') }}
                                @else
                                    <span style="color: var(--muted); font-style: italic;">Belum ada data</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if ($member->status == 'Belum Bayar')
                                    <a href="{{ route('kas.create', [
                                            'type' => 'in', 
                                            'member_id' => $member->id, 
                                            'month_year' => $filterMonth
                                        ]) }}" class="badge-status type-in" style="text-decoration: none;">
                                        <i class="fas fa-hand-holding-usd"></i> Bayar Kas
                                    </a>
                                @else
                                    @if ($member->last_payment_id)
                                    <form action="{{ route('kas.destroy', $member->last_payment_id) }}" method="POST" onsubmit="return confirm('Batalkan transaksi iuran ini?');" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-batal-aksi">
                                            <i class="fas fa-undo"></i> Batal
                                        </button>
                                    </form>
                                    @else
                                        <span style="color: var(--muted); font-size: 0.9em;">-</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--muted);">Tidak ada data anggota ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <span>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA</span>
        <span><i class="fas fa-shield-alt"></i> Wira Laga Santri</span>
    </div>
</div>

</body>
</html>