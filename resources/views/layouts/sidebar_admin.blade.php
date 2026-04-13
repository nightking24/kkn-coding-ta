<h3>Pembagian Kelompok KKN</h3>

<ul class="menu">

    <li>
        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>
    </li>

    <li>
        <a href="#">Periode KKN</a>
        <div class="submenu">
            <a href="/periode">Data Periode</a>
            <a href="/periode/create">Tambah Periode</a>
        </div>
    </li>

    <li>
        <a href="#">Kelompok</a>
        <div class="submenu">
            <a href="/kelompok">Data Kelompok</a>
            <a href="/kelompok/create">Tambah Kelompok</a>
        </div>
    </li>

    <li>
        <a href="#">DPL</a>
        <div class="submenu">
            <a href="/dpl">Data DPL</a>
            <a href="/dpl/create">Tambah DPL</a>
        </div>
    </li>

    <li>
        <a href="#">APL</a>
        <div class="submenu">
            <a href="/apl">Data APL</a>
            <a href="/apl/create">Tambah APL</a>
        </div>
    </li>

    <li>
        <a href="/import">Import Peserta</a>
    </li>

    <li>
        <a href="#">Hasil Randomisasi</a>
        <div class="submenu">
            <a href="{{ route('hasil.pembagian') }}"
                class="{{ request()->routeIs('hasil.pembagian') ? 'active' : '' }}">
                Hasil Pembagian
            </a>
            <a href="{{ route('halaman.tukar', ['periode_id' => session('periode_id')]) }}">
                Tukar Peserta
            </a>

            <a href="{{ route('halaman.pindah', ['periode_id' => session('periode_id')]) }}">
                Pindah Peserta
            </a>
        </div>
    </li>

    <li>
        <a href="/log-aktivitas" class="{{ request()->is('log-aktivitas') ? 'active' : '' }}">
            Log Aktivitas
        </a>
    </li>

    <li style="margin-top:30px;">
        <a href="/logout" class="btn-red" style="display:block; text-align:center;">
            Logout
        </a>
    </li>

</ul>