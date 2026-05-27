@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="card shadow-sm" style="border-radius:12px;">
            <div class="card-body">

                <h4 class="mb-3">Hasil Randomisasi Kelompok</h4>

                @php
                    $berhasil = collect($data)->where('status', 'ok');
                    $gagal = collect($data)->where('status', 'melanggar_rule');
                    $total_kelompok = $berhasil->pluck('nomor_kelompok')->unique()->count();
                @endphp

                <div style="display:flex; gap:30px; margin-bottom:15px;">
                    <p class="text-muted">Total Peserta: <b>{{ count($data) }}</b></p>
                    <p class="text-muted">Total Kelompok: <b>{{ $total_kelompok }}</b></p>
                </div>

                <h5 class="text-success mt-4">✔ Peserta Berhasil</h5>

                <div class="table-responsive">
                    <table id="table-berhasil" class="table table-bordered table-striped">
                        <thead class="table-dark" style="background: #343a40; color: white;">
                            <tr>
                                <th style="text-align: center; padding: 12px;">No</th>
                                <th style="text-align: center; padding: 12px;">Kelompok</th>
                                <th style="text-align: center; padding: 12px;">NIM</th>
                                <th style="text-align: center; padding: 12px;">Nama</th>
                                <th style="text-align: center; padding: 12px;">Prodi</th>
                                <th style="text-align: center; padding: 12px;">No Telp</th>
                                <th style="text-align: center; padding: 12px;">Gender</th>
                                <th style="text-align: center; padding: 12px;">Bahasa Jawa</th>
                                <th style="text-align: center; padding: 12px;">Riwayat Penyakit</th>
                                <th style="text-align: center; padding: 12px;">Kebutuhan Khusus</th>
                                <th style="text-align: center; padding: 12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($berhasil as $i => $d)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="text-align: center; padding: 12px;">{{ $i + 1 }}</td>
                                    <td style="text-align: center; padding: 12px;">K{{ $d['nomor_kelompok'] }}</td>
                                    <td style="text-align: center; padding: 12px;">{{ $d['nim'] }}</td>
                                    <td style="text-align: left; padding: 12px;">{{ $d['nama'] }}</td>
                                    <td style="text-align: center; padding: 12px;">{{ $d['prodi'] }}</td>
                                    <td style="text-align: center; padding: 12px;">{{ $d['no_telp'] ?? '-' }}</td>

                                    <td style="text-align: center; padding: 12px;">{{ $d['gender'] }}</td>

                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['bahasa_jawa'] == 1 ? 'Bisa' : 'Tidak' }}
                                    </td>

                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['riwayat_penyakit'] == 1 ? 'Ya' : 'Tidak' }}
                                    </td>

                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['berkebutuhan_khusus'] == 1 ? 'Ya' : 'Tidak' }}
                                    </td>

                                    <td style="text-align: center; padding: 12px;">
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
                        <thead class="table-danger" style="background: #f8d7da; color: #721c24;">
                            <tr>
                                <th style="text-align: center; padding: 12px;">No</th>
                                <th style="text-align: center; padding: 12px;">NIM</th>
                                <th style="text-align: center; padding: 12px;">Nama</th>
                                <th style="text-align: center; padding: 12px;">Prodi</th>
                                <th style="text-align: center; padding: 12px;">No Telp</th>
                                <th style="text-align: center; padding: 12px;">Gender</th>
                                <th style="text-align: center; padding: 12px;">Bahasa Jawa</th>
                                <th style="text-align: center; padding: 12px;">Riwayat Penyakit</th>
                                <th style="text-align: center; padding: 12px;">Kebutuhan Khusus</th>
                                <th style="text-align: center; padding: 12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gagal as $i => $d)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="text-align: center; padding: 12px;">{{ $i + 1 }}</td>
                                    <td style="text-align: center; padding: 12px;">{{ $d['nim'] }}</td>
                                    <td style="text-align: left; padding: 12px;">{{ $d['nama'] }}</td>
                                    <td style="text-align: center; padding: 12px;">{{ $d['prodi'] }}</td>
                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['no_telp'] ?? '-' }}
                                    </td>
                                    <td style="text-align: center; padding: 12px;">{{ $d['gender'] }}</td>

                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['bahasa_jawa'] == 1 ? 'Bisa' : 'Tidak' }}
                                    </td>

                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['riwayat_penyakit'] == 1 ? 'Ya' : 'Tidak' }}
                                    </td>

                                    <td style="text-align: center; padding: 12px;">
                                        {{ $d['berkebutuhan_khusus'] == 1 ? 'Ya' : 'Tidak' }}
                                    </td>

                                    <td style="text-align: center; padding: 12px;">
                                        <span class="badge bg-danger">
                                            Belum dapat kelompok
                                        </span>
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