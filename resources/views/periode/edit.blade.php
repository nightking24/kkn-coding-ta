@extends('layouts.app')

@section('content')

    <div class="card">

        <h2>Edit Periode KKN</h2>

        {{-- ERROR --}}
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

        {{-- SUCCESS --}}
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

        {{-- FORM EDIT --}}
        <form action="/periode/update/{{ $periode->id_periode }}" method="POST">
            @csrf

            <div style="margin-bottom:15px;">
                <label>Nama KKN</label>
                <input type="text" name="nama_kkn" value="{{ old('nama_kkn', $periode->nama_kkn) }}"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Tahun</label>
                <input type="number" name="tahun_kkn" value="{{ old('tahun_kkn', $periode->tahun_kkn) }}"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Lokasi</label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $periode->lokasi) }}"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $periode->tanggal_mulai) }}"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $periode->tanggal_selesai) }}"
                    style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Status</label>
                <select name="status" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;">

                    <option value="0" {{ $periode->status == 0 ? 'selected' : '' }}>
                        Tidak Aktif
                    </option>

                    <option value="1" {{ $periode->status == 1 ? 'selected' : '' }}>
                        Aktif
                    </option>

                </select>
            </div>

            <div style="margin-top:20px;">
                <a href="/periode" class="btn btn-gray">← Kembali</a>
                <button type="submit" class="btn btn-blue">Update</button>
            </div>

        </form>

    </div>

@endsection