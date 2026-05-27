@extends('layouts.app')

@section('content')

    <div class="card">

        <div class="card-header">
            <h2>Data Kelompok</h2>
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
                <thead style="background: #343a40; color: white;">
                    <tr>
                        <th style="text-align: center; padding: 12px;">No</th>
                        <th style="text-align: center; padding: 12px;">Kelompok</th>
                        <th style="text-align: center; padding: 12px;">Kecamatan</th>
                        <th style="text-align: center; padding: 12px;">Desa</th>
                        <th style="text-align: center; padding: 12px;">Dusun</th>
                        <th style="text-align: center; padding: 12px;" class="col-wrap">Nama Dukuh</th>
                        <th style="text-align: center; padding: 12px;">Kontak</th>
                        <th style="text-align: center; padding: 12px;">Tahun KKN</th>
                        <th style="text-align: center; padding: 12px;">DPL</th>
                        <th style="text-align: center; padding: 12px;">APL</th>
                        <th style="text-align: center; padding: 12px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($kelompok as $d)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="text-align: center; padding: 12px;">{{ $loop->iteration }}</td>
                            <td style="text-align: center; padding: 12px;">K{{ $d->nomor_kelompok }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->nama_kecamatan }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->desa }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->dusun }}</td>
                            <td style="text-align: left; padding: 12px;" class="col-wrap">{{ $d->nama_dukuh }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->nomor_telepon }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->tahun_kkn }}</td>
                            <td style="text-align: left; padding: 12px;">{{ optional($d->dpl)->nama ?? '-' }}</td>
                            <td style="text-align: left; padding: 12px;">{{ optional($d->apl)->nama ?? '-' }}</td>
                            <td style="text-align: center; padding: 12px;">
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
                { width: "150px", targets: 5 }  // nama dukuh
            ]
        });
    </script>
@endsection