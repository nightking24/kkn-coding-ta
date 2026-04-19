<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .sidebar-header {
        padding: 20px;
        background-color: #1e7e34;
        margin-bottom: 0;
    }

    .sidebar-header h3 {
        margin: 0;
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.4;
    }

    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: #1e7e34;
    }

    .menu > li {
        margin: 0;
        border-bottom: none;
    }

    .menu > li > a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        text-decoration: none;
        color: #ffffff;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .menu > li > a:hover {
        background-color: #0f5f37;
        color: #ffffff;
        padding-left: 25px;
    }

    .menu > li > a.active {
        background-color: #0f5f37;
        color: #ffffff;
        border-left: 4px solid #52d652;
        padding-left: 16px;
    }

    .menu-icon {
        margin-right: 10px;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
    }

    .menu-text {
        flex: 1;
    }

    .dropdown-toggle {
        font-size: 12px;
        transition: transform 0.3s ease;
        color: #ffffff;
    }

    .dropdown-toggle.active {
        transform: rotate(180deg);
    }

    .submenu {
        display: none;
        background-color: #0d5230;
        border-left: 3px solid #52d652;
    }

    .submenu.active {
        display: block;
    }

    .submenu a {
        display: flex;
        align-items: center;
        padding: 10px 20px 10px 40px;
        text-decoration: none;
        color: #d4edda;
        font-size: 14px;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .submenu a:hover {
        background-color: #0a3d24;
        color: #ffffff;
        border-left-color: #52d652;
        padding-left: 45px;
    }

    .submenu a.active {
        background-color: #0a3d24;
        color: #ffffff;
        border-left-color: #52d652;
        font-weight: 500;
    }

    .submenu .menu-icon {
        margin-right: 8px;
        font-size: 14px;
    }

    .menu-category {
        display: none;
        padding: 12px 20px;
        font-size: 11px;
        font-weight: 700;
        color: #d4edda;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 5px;
        margin-bottom: 0;
    }

    .menu-single-link > a {
        display: flex !important;
        align-items: center;
        padding: 12px 20px !important;
        color: #ffffff !important;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .menu-single-link > a:hover {
        background-color: #0f5f37;
        color: #ffffff;
        padding-left: 25px;
    }

    .menu-single-link > a.active {
        background-color: #0f5f37;
        color: #ffffff;
        border-left: 4px solid #52d652;
        padding-left: 16px;
    }

    .btn-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 20px 20px 20px;
        padding: 12px;
        background-color: #dc3545;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-logout:hover {
        background-color: #c82333;
        transform: translateY(-2px);
    }
</style>

<div class="sidebar-header">
    <h3>Pembagian Kelompok KKN Reguler</h3>
</div>

<ul class="menu">
    <!-- Dashboard -->
    <li class="menu-single-link">
        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 menu-icon"></i>
            <span class="menu-text">Dashboard</span>
        </a>
    </li>

    <!-- Data Master -->
    <li class="menu-category">Data Master</li>

    <li>
        <a href="#" class="dropdown-header" onclick="toggleDropdown(this, event)">
            <span style="display: flex; align-items: center; flex: 1;">
                <i class="bi bi-calendar menu-icon"></i>
                <span class="menu-text">Periode KKN</span>
            </span>
            <span class="dropdown-toggle">▼</span>
        </a>
        <div class="submenu {{ Request::is('periode*') ? 'active' : '' }}">
            <a href="/periode" class="{{ request()->is('periode') && !request()->is('periode/create') ? 'active' : '' }}">
                <i class="bi bi-list-check menu-icon"></i>
                <span>Data Periode</span>
            </a>
            <a href="/periode/create" class="{{ request()->is('periode/create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle menu-icon"></i>
                <span>Tambah Periode</span>
            </a>
        </div>
    </li>

    <li>
        <a href="#" class="dropdown-header" onclick="toggleDropdown(this, event)">
            <span style="display: flex; align-items: center; flex: 1;">
                <i class="bi bi-people menu-icon"></i>
                <span class="menu-text">Kelompok</span>
            </span>
            <span class="dropdown-toggle">▼</span>
        </a>
        <div class="submenu {{ Request::is('kelompok*') ? 'active' : '' }}">
            <a href="/kelompok" class="{{ request()->is('kelompok') && !request()->is('kelompok/create') ? 'active' : '' }}">
                <i class="bi bi-list-check menu-icon"></i>
                <span>Data Kelompok</span>
            </a>
            <a href="/kelompok/create" class="{{ request()->is('kelompok/create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle menu-icon"></i>
                <span>Tambah Kelompok</span>
            </a>
        </div>
    </li>

    <li>
        <a href="#" class="dropdown-header" onclick="toggleDropdown(this, event)">
            <span style="display: flex; align-items: center; flex: 1;">
                <i class="bi bi-mortarboard menu-icon"></i>
                <span class="menu-text">DPL</span>
            </span>
            <span class="dropdown-toggle">▼</span>
        </a>
        <div class="submenu {{ Request::is('dpl*') ? 'active' : '' }}">
            <a href="/dpl" class="{{ request()->is('dpl') && !request()->is('dpl/create') ? 'active' : '' }}">
                <i class="bi bi-list-check menu-icon"></i>
                <span>Data DPL</span>
            </a>
            <a href="/dpl/create" class="{{ request()->is('dpl/create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle menu-icon"></i>
                <span>Tambah DPL</span>
            </a>
        </div>
    </li>

    <li>
        <a href="#" class="dropdown-header" onclick="toggleDropdown(this, event)">
            <span style="display: flex; align-items: center; flex: 1;">
                <i class="bi bi-person-badge menu-icon"></i>
                <span class="menu-text">APL</span>
            </span>
            <span class="dropdown-toggle">▼</span>
        </a>
        <div class="submenu {{ Request::is('apl*') ? 'active' : '' }}">
            <a href="/apl" class="{{ request()->is('apl') && !request()->is('apl/create') ? 'active' : '' }}">
                <i class="bi bi-list-check menu-icon"></i>
                <span>Data APL</span>
            </a>
            <a href="/apl/create" class="{{ request()->is('apl/create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle menu-icon"></i>
                <span>Tambah APL</span>
            </a>
        </div>
    </li>

    <!-- Data Peserta -->
    <li class="menu-category">Data Peserta</li>

    <li class="menu-single-link">
        <a href="/import" class="{{ request()->is('import') ? 'active' : '' }}">
            <i class="bi bi-cloud-upload menu-icon"></i>
            <span class="menu-text">Import Peserta</span>
        </a>
    </li>

    <!-- Randomisasi -->
    <li class="menu-category">Randomisasi</li>

    <li>
        <a href="#" class="dropdown-header" onclick="toggleDropdown(this, event)">
            <span style="display: flex; align-items: center; flex: 1;">
                <i class="bi bi-shuffle menu-icon"></i>
                <span class="menu-text">Hasil Randomisasi</span>
            </span>
            <span class="dropdown-toggle">▼</span>
        </a>
        <div class="submenu {{ Request::is('hasil*') || Request::is('tukar*') || Request::is('pindah*') ? 'active' : '' }}">
            <a href="{{ route('hasil.pembagian') }}" class="{{ request()->routeIs('hasil.pembagian') ? 'active' : '' }}">
                <i class="bi bi-graph-up menu-icon"></i>
                <span>Hasil Pembagian</span>
            </a>
            <a href="{{ route('halaman.tukar', ['periode_id' => session('periode_id')]) }}" class="{{ request()->routeIs('halaman.tukar') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right menu-icon"></i>
                <span>Tukar Peserta</span>
            </a>
            <a href="{{ route('halaman.pindah', ['periode_id' => session('periode_id')]) }}" class="{{ request()->routeIs('halaman.pindah') ? 'active' : '' }}">
                <i class="bi bi-arrows-move menu-icon"></i>
                <span>Pindah Peserta</span>
            </a>
        </div>
    </li>

    <!-- Monitoring -->
    <li class="menu-category">Monitoring</li>

    <li class="menu-single-link">
        <a href="/log-aktivitas" class="{{ request()->is('log-aktivitas') ? 'active' : '' }}">
            <i class="bi bi-file-text menu-icon"></i>
            <span class="menu-text">Log Aktivitas</span>
        </a>
    </li>

</ul>

<a href="/logout" class="btn-logout">
    <i class="bi bi-box-arrow-right"></i>
    <span>Logout</span>
</a>

<script>
    function toggleDropdown(element, event) {
        event.preventDefault();
        const submenu = element.nextElementSibling;
        const toggle = element.querySelector('.dropdown-toggle');
        
        // Close other open submenus
        document.querySelectorAll('.submenu.active').forEach(menu => {
            if (menu !== submenu) {
                menu.classList.remove('active');
                menu.previousElementSibling.querySelector('.dropdown-toggle').classList.remove('active');
            }
        });
        
        // Toggle current submenu
        submenu.classList.toggle('active');
        toggle.classList.toggle('active');
    }

    // Auto-open submenu jika ada menu item yang active
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.submenu.active').forEach(menu => {
            const toggle = menu.previousElementSibling.querySelector('.dropdown-toggle');
            if (toggle) toggle.classList.add('active');
        });
    });
</script>