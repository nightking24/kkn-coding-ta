@extends('layouts.app')

@section('content')

    <div class="card">
        <h2>Edit DPL</h2>

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

        <form action="/dpl/update/{{ $data->nik }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ $data->nik }}" pattern="[0-9]*"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="16">
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $data->nama }}" list="list-dpl">
                    <datalist id="list-dpl">
                        @foreach($dplList as $dpl)
                            <option value="{{ $dpl->nama }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label>Fakultas</label>
                    <input type="text" name="fakultas" value="{{ $data->fakultas ?? '' }}" class="form-control"
                        maxlength="100">
                </div>

                <div class="form-group">
                    <label>Prodi</label>
                    <input type="text" name="prodi" value="{{ $data->prodi ?? '' }}" class="form-control" maxlength="100">
                </div>

                <div class="form-group">
                    <label>Email <span style="color: #999; font-size: 12px;">(Wajib diisi)</span></label>
                    <input type="text" name="email" value="{{ $data->email }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="no_telp" class="form-control" value="{{ $data->no_telp }}" pattern="[0-9]*"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15">
                </div>

            </div>

            <div style="margin-top:20px;">
                <button class="btn btn-green">Update</button>
                <a href="/dpl" class="btn btn-red">Batal</a>
            </div>
        </form>
    </div>

@endsection