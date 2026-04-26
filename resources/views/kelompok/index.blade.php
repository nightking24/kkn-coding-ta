@extends('layouts.app')

@section('content')

    <div class="card">

        <div class="card-header">
            <h2>Data Kelompok</h2>
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

            <a href="/kelompok/create" class="btn btn-green">
                + Tambah
            </a>

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
        </div>

        <div class="table-wrapper">
            <form method="GET" style="margin-bottom: 15px;">
                <label>Pilih Periode:</label>
                <select name="periode_id" onchange="this.form.submit()" class="form-control" style="width: 200px;">
                    @foreach($periodes as $p)
                        <option value="{{ $p->id_periode }}" {{ $periode_id == $p->id_periode ? 'selected' : '' }}>
                            {{ $p->tahun_kkn }}
                        </option>
                    @endforeach
                </select>
            </form>
            <table id="kelompokTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kelompok</th>
                        <th>Desa</th>
                        <th>Dusun</th>
                        <th class="col-wrap">Nama Dukuh</th>
                        <th class="col-wrap">Tuan Rumah</th>
                        <th>Kontak</th>
                        <th class="col-wrap">Alamat</th>
                        <th>Faskes</th>
                        <th>Kapasitas</th>
                        <th>Semester</th>
                        <th>Tahun</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>DPL</th>
                        <th>APL</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($kelompok as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->nomor_kelompok }}</td>
                            <td>{{ $d->desa }}</td>
                            <td>{{ $d->dusun }}</td>
                            <td class="col-wrap">{{ $d->nama_dukuh }}</td>
                            <td class="col-wrap">{{ optional($d->tuanRumah)->nama_tuan_rumah ?? '-' }}</td>
                            <td>{{ $d->nomor_telepon }}</td>
                            <td class="col-wrap" title="{{ $d->alamat }}">
                                {{ $d->alamat }}
                            </td>
                            <td>{{ $d->faskes ? 'Ya' : 'Tidak' }}</td>
                            <td>{{ $d->kapasitas }}</td>
                            <td>{{ $d->semester }}</td>
                            <td>{{ $d->tahun_kkn }}</td>
                            <td>{{ $d->latitude }}</td>
                            <td>{{ $d->longitude }}</td>
                            <td>{{ optional($d->dpl)->nama ?? '-' }}</td>
                            <td>{{ optional($d->apl)->nama ?? '-' }}</td>
                            <td>
                                <a href="/kelompok/edit/{{ $d->id_kelompok }}" class="btn btn-blue">Edit</a>
                                <a href="/kelompok/delete/{{ $d->id_kelompok }}" class="btn btn-red">Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $('#kelompokTable').DataTable({
            scrollX: true,
            autoWidth: false,

            columnDefs: [
                { width: "250px", targets: 7 }, // alamat
                { width: "150px", targets: 4 }, // nama dukuh
                { width: "150px", targets: 5 }  // tuan rumah
            ]
        });
    </script>
@endsection