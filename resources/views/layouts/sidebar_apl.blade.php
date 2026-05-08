<div style="padding: 25px 20px; background: linear-gradient(135deg, #1e7e34 0%, #0f5f37 100%); border-bottom: 4px solid #52d652;">
    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: 0.5px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
        👨‍🏫 Pembagian Kelompok KKN Reguler
    </h3>
    <p style="margin: 8px 0 0 0; font-size: 12px; color: #e0f2f1; font-weight: 500;">APL (Asisten Pendamping Lapangan)</p>
</div>

<style>
    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: #1e7e34;
    }

    .menu li {
        margin: 0;
    }

    .menu a {
        display: flex !important;
        align-items: center;
        padding: 12px 20px !important;
        text-decoration: none;
        color: #ffffff !important;
        transition: all 0.3s ease;
        border-left: 4px solid transparent !important;
    }

    .menu a:hover {
        background-color: #1a7d4f !important;
        color: #ffffff !important;
        border-left-color: #52d652 !important;
    }

    .menu a.active {
        background-color: #14a855 !important;
        color: #ffffff !important;
        border-left: 4px solid #52d652 !important;
        box-shadow: inset 2px 0 0 #52d652 !important;
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
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-logout:hover {
        background-color: #c82333 !important;
        transform: translateY(-2px);
    }
</style>

<ul class="menu">
    <li>
        <a href="/hasil-apl-new" class="{{ request()->is('hasil-apl-new') ? 'active' : '' }}">
            <span style="margin-right: 12px; font-size: 18px;">📄</span>
            <span>Data Kelompok</span>
        </a>
    </li>
</ul>

<a href="/logout" class="btn-logout">
    <span style="font-size: 18px;">🚪</span>
    <span>Logout</span>
</a>