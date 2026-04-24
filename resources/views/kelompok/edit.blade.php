@extends('layouts.app')

@section('content')

    <div class="card">

        <h2 style="margin-bottom:20px;">Edit Kelompok</h2>

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

        <form action="/kelompok/update/{{ $data->id_kelompok }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>Nomor Kelompok</label>
                    <input type="text" name="nomor_kelompok" class="form-control" value="{{ $data->nomor_kelompok }}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" inputmode="numeric">
                </div>

                <div class="form-group">
                    <label>Desa</label>
                    <input type="text" name="desa" class="form-control" value="{{ $data->desa }}">
                </div>

                <div class="form-group">
                    <label>Dusun</label>
                    <input type="text" name="dusun" class="form-control" value="{{ $data->dusun }}">
                </div>

                <div class="form-group">
                    <label>Nama Dukuh</label>
                    <input type="text" name="nama_dukuh" class="form-control" value="{{ $data->nama_dukuh }}">
                </div>

                <div class="form-group">
                    <label>Nama Tuan Rumah</label>
                    <input type="text" name="nama_tuan_rumah" class="form-control" value="{{ $data->nama_tuan_rumah }}">
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" class="form-control" value="{{ $data->nomor_telepon }}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" inputmode="numeric">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ $data->alamat }}">
                </div>

                <div class="form-group">
                    <label>Faskes</label>
                    <select name="faskes" class="form-control">
                        <option value="1" {{ $data->faskes ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ !$data->faskes ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kapasitas</label>
                    <input type="number" name="kapasitas" class="form-control" value="{{ $data->kapasitas }}">
                </div>

                <div class="form-group">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        <option value="Gasal" {{ $data->semester == 'Gasal' ? 'selected' : '' }}>Gasal</option>
                        <option value="Genap" {{ $data->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun KKN</label>
                    <input type="number" name="tahun_kkn" class="form-control" value="{{ $data->tahun_kkn }}">
                </div>

                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" step="any" name="latitude" class="form-control" value="{{ $data->latitude }}"
                        required min="-90" max="90">
                </div>

                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" step="any" name="longitude" class="form-control" value="{{ $data->longitude }}"
                        required min="-180" max="180">
                    <div style="margin-top:-10px; margin-bottom:10px;">
                        <small style="color: gray;">
                            Contoh koordinat: Latitude -7.7956, Longitude 110.3695
                        </small>
                    </div>

                </div>

                <div style="margin-top:25px; display:flex; gap:10px;">
                    <a href="/kelompok" class="btn btn-gray">← Kembali</a>
                    <button type="submit" class="btn btn-blue">Update</button>
                </div>

        </form>

    </div>

@endsection