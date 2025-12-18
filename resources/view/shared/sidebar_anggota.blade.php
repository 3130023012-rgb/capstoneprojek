<div class="sidebar">
    <div class="sidebar-header">UKM PAGARNUSA</div>
    <ul class="sidebar-menu">

        {{-- 1. Dashboard --}}
        <li><a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">Dashboard</a></li>

        {{-- 2. Biodata (Profile) --}}
        <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Biodata & Akun Saya</a></li>

        {{-- 3. Status Kas --}}
        <li><a href="{{ route('kas.status') }}" class="{{ request()->routeIs('kas.status') ? 'active' : '' }}">Status Kas</a></li>

        {{-- 4. Kehadiran (Absensi) --}}
        <li><a href="{{ route('absensi.index') }}" class="{{ request()->routeIs('absensi.index') ? 'active' : '' }}">Kehadiran</a></li>

        {{-- 5. Logout --}}
        <li style="margin-top: 20px; border-top: 1px solid rgba(0,0,0,.06);">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               style="color: var(--color-danger); font-weight: 900;">
                Logout
            </a>
        </li>
    </ul>
</div>