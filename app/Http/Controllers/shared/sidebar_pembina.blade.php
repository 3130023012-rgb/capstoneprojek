<div class="sidebar">
    <div class="sidebar-header">Pembina UKM PAGARNUSA</div>
    <ul class="sidebar-menu">

        {{-- 1. Dashboard --}}
        <li><a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">Dashboard</a></li>

        {{-- 2. Peninjauan Laporan --}}
        <li>
            <details open>
                <summary>Peninjauan Laporan</summary>
                <ul class="sidebar-dropdown">
                    <li><a href="{{ route('laporan.absensi') }}">Laporan Absensi</a></li>
                    <li><a href="{{ route('laporan.kas') }}">Laporan Kas</a></li>
                    <li><a href="{{ route('laporan.kegiatan') }}">Laporan Kegiatan</a></li>
                </ul>
            </details>
        </li>
        
        {{-- 3. Logout --}}
        <li style="margin-top: 20px; border-top: 1px solid rgba(0,0,0,.06);">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               style="color: var(--color-danger); font-weight: 900;">
                Logout
            </a>
        </li>
    </ul>
</div>