@extends('layouts.app')

@section('content')

    <div class="card">
        <h2>Tambah APL</h2>
        
        @if(session('error'))
            <div style="
                background:#f8d7da;
                color:#721c24;
                padding:12px;
                border-radius:8px;
                margin-bottom:15px;
                border-left:5px solid #dc3545;
            ">
                {{ session('error') }}
            </div>
        @endif

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

        <form action="/apl/store" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>NIM</label>
                    <input type="text" name="nim" class="form-control" value="{{ old('nim') }}" pattern="[0-9]*"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}" pattern="[0-9]*"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

            </div>

            <div style="margin-top:20px;">
                <button class="btn btn-green">Simpan</button>
                <a href="/apl" class="btn btn-red">Batal</a>
            </div>
        </form>
    </div>

@endsection