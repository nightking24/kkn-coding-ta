@extends('layouts.app')

@section('content')

    <div class="card">

        <h2>Tambah Periode KKN</h2>
        @if ($errors->any())
            <div style="
                        background:#f8d7da;
                        color:#721c24;
                        padding:12px;
                        border-radius:8px;
                        margin-bottom:15px;
                        border-left:5px solid #dc3545;
                    ">
                <b>Terjadi kesalahan:</b>
                <ul style="margin:5px 0 0 15px;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div style="
                background:#d4edda;
                color:#155724;
                padding:12px;
                border-radius:8px;
                margin-bottom:15px;
                border-left:5px solid #28a745;
            ">
                {{ session('success') }}
            </div>
        @endif
        <form action="/periode/store" method="POST">
            @csrf

            <div style="margin-bottom:15px;">
                <label>Nama KKN</label>
                <input type="text" name="nama_kkn" placeholder="Contoh: KKN Reguler"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Tahun</label>
                <input type="number" name="tahun_kkn" placeholder="2027"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Lokasi</label>
                <input type="text" name="lokasi" placeholder="Contoh: Pacitan"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Status</label>
                <select name="status" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
                    <option value="0">Tidak Aktif</option>
                    <option value="1">Aktif</option>
                </select>
            </div>

            <div style="margin-top:20px;">
                <a href="/periode" class="btn btn-gray">← Kembali</a>
                <button type="submit" class="btn btn-green">Simpan</button>
            </div>

        </form>

    </div>

@endsection