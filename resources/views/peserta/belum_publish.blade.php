@extends('layouts.app')

@section('content')

<style>
    .pending-container {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .pending-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(30, 126, 52, 0.15);
        padding: 40px;
        max-width: 500px;
        text-align: center;
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pending-icon {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.8;
    }

    .pending-title {
        font-size: 24px;
        font-weight: 600;
        color: #1e7e34;
        margin-bottom: 15px;
    }

    .pending-message {
        font-size: 16px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .pending-info {
        background: #f0f8f4;
        border-left: 4px solid #1e7e34;
        padding: 15px;
        border-radius: 6px;
        color: #1e7e34;
        font-size: 14px;
    }
</style>

<div class="pending-container">
    <div class="pending-card">
        <div class="pending-icon">⏳</div>
        <h2 class="pending-title">Menunggu Publikasi</h2>
        <p class="pending-message">
            Hasil pembagian kelompok KKN belum dipublikasikan oleh admin.
        </p>
        <div class="pending-info">
            <strong>ℹ️ Info:</strong> Silakan tunggu pengumuman lebih lanjut dari admin. Kami akan segera menampilkan hasil pembagian kelompok.
        </div>
    </div>
</div>

@endsection