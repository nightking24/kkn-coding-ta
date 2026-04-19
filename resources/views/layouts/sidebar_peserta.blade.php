<div style="padding: 20px;">
    <h3 style="margin: 0 0 30px 0; font-size: 24px; font-weight: 700;">Pembagian Kelompok KKN Reguler</h3>
</div>

<ul class="menu">
    <li>
        <a href="/hasil-peserta" class="{{ request()->is('hasil-peserta') ? 'active' : '' }}"
            style="display: flex; align-items: center; padding: 14px 20px; border-left: 3px solid {{ request()->is('hasil-peserta') ? '#1abc9c' : 'transparent' }};">
            <span style="margin-right: 12px; font-size: 18px;">📄</span>
            <span>Hasil Kelompok</span>
        </a>
    </li>

    <li style="margin-top: 40px; padding: 0 20px;">
        <a href="/logout"
            style="display: block; background: #dc3545; color: white; padding: 12px 16px; text-decoration: none; border-radius: 6px; text-align: center; transition: 0.3s; font-weight: 500;">
            🚪 Logout
        </a>
    </li>
</ul>

<style>
    .menu a[href="/logout"]:hover {
        background: #c82333 !important;
    }
</style>