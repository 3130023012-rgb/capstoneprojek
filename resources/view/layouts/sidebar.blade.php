@php
    // Ambil nama route saat ini untuk menentukan item mana yang aktif
    $currentRoute = Route::currentRouteName();
@endphp

<div class="sidebar">
    <div class="sidebar-header">Admin UKM PAGARNUSA</div>
    <ul class="sidebar-menu">
        
        {{-- 1. Dashboard --}}
        <li><a href="{{ route('dashboard.index') }}" class="{{ $currentRoute == 'dashboard.index' ? 'active' : '' }}">Dashboard</a></li>
        
        {{-- 2. MASTER DATA PENGGUNA (Dropdown) --}}
        @php
            $masterDataRoutes = ['member.index', 'member.profile', 'konten.index', 'member.user.create', 'member.edit'];
            $isMasterDataActive = in_array($currentRoute, $masterDataRoutes);
        @endphp
        <li>
            <details {{ $isMasterDataActive ? 'open' : '' }}> 
                <summary class="{{ $isMasterDataActive ? 'active' : '' }}">Master Data Pengguna</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('member.index') }}" class="{{ $currentRoute == 'member.index' ? 'active' : '' }}">Manajemen Anggota</a></li>
                    <li><a href="{{ route('konten.index') }}" class="{{ $currentRoute == 'konten.index' ? 'active' : '' }}">Manajemen Konten</a></li> 
                </ul>
            </details>
        </li>
        
        {{-- 3. Absensi --}}
        <li><a href="{{ route('absensi.index') }}" class="{{ $currentRoute == 'absensi.index' ? 'active' : '' }}">Absensi</a></li>
        
        {{-- 4. Transaksi (Dropdown) --}}
        @php
            $transaksiRoutes = ['kas.index', 'kas.create', 'kegiatan.index', 'kegiatan.create'];
            $isTransaksiActive = in_array($currentRoute, $transaksiRoutes);
        @endphp
        <li>
            <details {{ $isTransaksiActive ? 'open' : '' }}>
                <summary class="{{ $isTransaksiActive ? 'active' : '' }}">Transaksi</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('kas.index') }}" class="{{ $currentRoute == 'kas.index' ? 'active' : '' }}">Kas</a></li>
                    <li><a href="{{ route('kegiatan.index') }}" class="{{ $currentRoute == 'kegiatan.index' ? 'active' : '' }}">Kegiatan</a></li>
                </ul>
            </details>
        </li>
        
        {{-- 5. Laporan (Dropdown) --}}
        @php
            $laporanRoutes = ['laporan.absensi', 'laporan.kas', 'laporan.kegiatan'];
            $isLaporanActive = in_array($currentRoute, $laporanRoutes);
        @endphp
        <li>
            <details {{ $isLaporanActive ? 'open' : '' }}>
                <summary class="{{ $isLaporanActive ? 'active' : '' }}">Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}" class="{{ $currentRoute == 'laporan.absensi' ? 'active' : '' }}">Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}" class="{{ $currentRoute == 'laporan.kas' ? 'active' : '' }}">Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}" class="{{ $currentRoute == 'laporan.kegiatan' ? 'active' : '' }}">Kegiatan</a></li> 
                </ul>
            </details>
        </li>
        
        {{-- 6. Logout --}}
        <li style="margin-top: 20px; border-top: 1px solid #eee;">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--color-danger); font-weight: bold;">
                Logout
            </a>
        </li>
    </ul>
</div>