<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UKM PAGARNUSA - Kehadiran</title>
    
    <style>
/* Variabel Warna */
:root {
    --color-primary: #007bff; /* Biru */
    --color-success: #28a745; /* Hijau */
    --color-warning: #ffc107; /* Kuning */
    --color-danger: #dc3545; /* Merah */
    --color-dark: #343a40;
    --color-light: #f8f9fa;
    --color-white: #fff;
}

body {
    font-family: sans-serif;
    margin: 0;
    background-color: var(--color-light);
}

.main-container {
    display: flex;
    min-height: 100vh;
}

/* --- Sidebar Style --- */
.sidebar {
    width: 220px;
    background-color: var(--color-white);
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    padding-top: 20px;
}
.sidebar-title {
    padding: 10px 15px;
    margin-bottom: 20px;
    font-size: 1.2em;
    font-weight: bold;
}
.sidebar-link a {
    padding: 10px 15px;
    text-decoration: none;
    font-size: 16px;
    color: var(--color-dark);
    display: block;
    border-left: 3px solid transparent;
}
.sidebar-link a:hover, .sidebar-link .active {
    background-color: #e9ecef;
    border-left-color: var(--color-success);
    font-weight: bold;
}

/* --- Content Style --- */
.content {
    flex-grow: 1;
    padding: 30px;
}
.section-box {
    background-color: var(--color-white);
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}
.section-title {
    text-align: center;
    margin-bottom: 20px;
    color: var(--color-dark);
}

/* --- Chart Style --- */
.chart-container {
    text-align: center;
    border: 1px dotted #adb5bd; /* Warna Blade */
    padding: 20px;
    border-radius: 8px;
    position: relative;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.chart-info {
    position: absolute;
    top: 20px;
    left: 20px;
    text-align: left;
    margin-bottom: 0;
}

.chart-info small { 
    color: var(--color-dark); /* Diperbesar dan diperjelas */
    font-size: 1.1em;
    font-weight: bold;
}

.chart-percentage-label {
    text-align: center;
    font-size: 1.1em;
    font-weight: 500;
    color: var(--color-dark);
    margin-bottom: 20px; 
    z-index: auto; 
}

.pie-chart {
    width: 250px;
    height: 250px;
    margin: 20px 0;
    border-radius: 50%;
    /* Logic CSS Cone Gradient untuk simulasi chart */
    background: conic-gradient(
        var(--color-success) 0% {{ $monthlyAttendanceData['present'] }}%, 
        var(--color-danger) {{ $monthlyAttendanceData['present'] }}% {{ $monthlyAttendanceData['present'] + $monthlyAttendanceData['absent'] }}%, 
        var(--color-warning) {{ $monthlyAttendanceData['present'] + $monthlyAttendanceData['absent'] }}% {{ $monthlyAttendanceData['present'] + $monthlyAttendanceData['absent'] + $monthlyAttendanceData['sick_leave'] }}%,
        var(--color-primary) {{ $monthlyAttendanceData['present'] + $monthlyAttendanceData['absent'] + $monthlyAttendanceData['sick_leave'] }}% 100%
    );
}
.chart-legend {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px; 
}
.legend-item {
    font-size: 0.9em;
    padding: 5px 10px;
    border-radius: 4px;
    color: var(--color-white);
}

.chart-activity-label {
    position: absolute; 
    bottom: 20px; 
    right: 20px; 
    font-size: 0.9em;
    color: #6c757d;
}

/* --- Table Style --- */
.table-responsive {
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}
table thead th {
    background-color: var(--color-dark);
    color: var(--color-white);
    padding: 12px 15px;
}
table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}
table tbody td {
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
}
.text-center {
    text-align: center;
}
.badge {
    display: inline-block;
    padding: 5px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    color: var(--color-dark);
}
.badge.success { background-color: var(--color-success); color: var(--color-white); }
.badge.warning { background-color: var(--color-warning); }
.badge.danger { background-color: var(--color-danger); color: var(--color-white); }
.badge.primary { background-color: var(--color-primary); color: var(--color-white); }


/* --- Footer Style --- */
.footer {
    background-color: var(--color-white);
    padding: 15px 30px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: center;
    gap: 30px; 
    font-size: 0.9em;
    color: var(--color-dark);
    padding-left: 150px; 
    padding-right: 30px; 
}
</style>
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <div class="sidebar-title">UKM PAGARNUSA</div>
        <div class="sidebar-link"><a href="#">Dashboard</a></div>
        <div class="sidebar-link"><a href="#">Biodata</a></div>
        <div class="sidebar-link"><a href="#">Status Kas</a></div>
        <div class="sidebar-link"><a href="{{ route('absensi') }}" class="active">Kehadiran</a></div>
    </div>

    <div class="content">
        @if (session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="section-box">
            <h2 class="section-title">Grafik Kehadiran</h2>

            {{-- FORM FILTER BULAN BARU --}}
            <form action="{{ route('absensi') }}" method="GET" style="text-align: right; margin-bottom: 20px;">
                <label for="month_filter" style="font-weight: bold; color: var(--color-dark);">Pilih Bulan:</label>
                
                @php
                    $currentFilter = request('month_filter', \Carbon\Carbon::now()->format('Y-m'));
                @endphp
                
                <select name="month_filter" id="month_filter" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                    @for ($i = 0; $i < 12; $i++)
                        @php
                            $date = \Carbon\Carbon::now()->subMonths($i);
                            $value = $date->format('Y-m');
                        @endphp
                        <option value="{{ $value }}" {{ $currentFilter == $value ? 'selected' : '' }}>
                            {{ $date->translatedFormat('F Y') }}
                        </option>
                    @endfor
                </select>
            </form>
            {{-- AKHIR FORM FILTER --}}
            
            <div class="chart-container">
                <div class="chart-info">
                    <small>Absensi Bulanan</small>
                </div>
                
                <p class="chart-percentage-label">Persentase</p>
                
                <div class="pie-chart"></div>

                <div class="chart-legend">
                    <span class="legend-item" style="background-color: var(--color-success);">Hadir ({{ $monthlyAttendanceData['present'] }}%)</span>
                    <span class="legend-item" style="background-color: var(--color-danger);">Absen ({{ $monthlyAttendanceData['absent'] }}%)</span>
                    <span class="legend-item" style="background-color: var(--color-warning); color: var(--color-dark);">Sakit ({{ $monthlyAttendanceData['sick_leave'] }}%)</span>
                    <span class="legend-item" style="background-color: var(--color-primary);">Izin ({{ $monthlyAttendanceData['permission'] }}%)</span>
                </div>

                <small class="chart-activity-label">Kegiatan</small>
            </div>
        </div>

        <a href="{{ route('absensi.create') }}" 
           style="
                display: inline-block; 
                background-color: var(--color-primary); 
                color: var(--color-white); 
                padding: 10px 15px; 
                text-decoration: none; 
                border-radius: 4px; 
                margin-bottom: 20px;
                font-size: 0.95em;
            ">
            + Tambah Data Kehadiran
        </a>
        <h2 class="section-title" style="margin-top: 40px;">Rincian Kehadiran Anggota</h2>
        <div class="section-box">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Anggota</th>
                            <th>NIM / NIA</th> <th>Nama Pelatih</th>
                            <th>Tanggal</th>
                            <th>Materi</th>
                            <th class="text-center">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detailedAttendance as $attendance)
                        <tr>
                            <td>{{ $attendance->member->name ?? 'Anggota Dihapus' }}</td>
                            <td>{{ $attendance->member->member_id ?? '-' }}</td> <td>{{ $attendance->activity->trainer->name ?? 'N/A' }}</td>
                            <td>{{ Carbon\Carbon::parse($attendance->activity->date)->translatedFormat('d F Y') }}</td>
                            <td>{{ $attendance->activity->material }}</td>
                            
                            <td class="text-center">
                                @php
                                    $statusText = [
                                        'present' => 'Hadir',
                                        'absent' => 'Absen',
                                        'sick_leave' => 'Sakit',
                                        'permission' => 'Izin',
                                    ];
                                    // Menentukan kelas berdasarkan status
                                    $badgeClass = 'warning';
                                    if ($attendance->status == 'present') $badgeClass = 'success';
                                    if ($attendance->status == 'absent') $badgeClass = 'danger';
                                    if ($attendance->status == 'permission') $badgeClass = 'primary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $statusText[$attendance->status] ?? $attendance->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada rincian kehadiran anggota yang tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>

<div class="footer">
    <span>&copy; {{ date('Y') }} UKM PAGARNUSA</span>
    <span>All rights reserved</span>
    <span>Kontak: info@pagarnusa.id</span>
</div>

</body>
</html>
