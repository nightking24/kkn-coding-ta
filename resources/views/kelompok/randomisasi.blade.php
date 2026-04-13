@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="card shadow-sm" style="border-radius:12px;">
            <div class="card-body">

                <h4 class="mb-3">Hasil Randomisasi Kelompok</h4>
                <p class="text-muted">Total Peserta: <b>{{ count($data) }}</b></p>

                @php
                    $berhasil = collect($data)->where('status', 'ok');
                    $gagal = collect($data)->where('status', 'melanggar_rule');
                @endphp

                <h5 class="text-success mt-4">✔ Peserta Berhasil</h5>

                <div class="table-responsive">
                    <table id="table-berhasil" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kelompok</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($berhasil as $i => $d)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>K{{ $d['nomor_kelompok'] }}</td>
                                    <td>{{ $d['nim'] }}</td>
                                    <td>{{ $d['nama'] }}</td>
                                    <td>{{ $d['prodi'] }}</td>
                                    <td>
                                        <span class="badge bg-success">OK</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h5 class="text-danger mt-5">⚠ Peserta Belum Mendapat Kelompok</h5>

                <div class="table-responsive">
                    <table id="table-gagal" class="table table-bordered table-striped">
                        <thead class="table-danger">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gagal as $i => $d)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $d['nim'] }}</td>
                                    <td>{{ $d['nama'] }}</td>
                                    <td>{{ $d['prodi'] }}</td>
                                    <td>
                                        <span class="badge bg-danger">Belum dapat kelompok</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">

                    <a href="{{ url('/import?periode_id=' . session('periode_id')) }}" class="btn btn-secondary">
                        ← Kembali
                    </a>

                    <form action="{{ url('/simpan-hasil?periode_id=' . session('periode_id')) }}" method="POST">
                        @csrf
                        <button class="btn btn-success">
                            Simpan & Lanjut →
                        </button>
                    </form>

                </div>

            </div>
        </div>

    </div>

@endsection