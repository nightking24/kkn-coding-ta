@extends('layouts.app')

@section('content')

    <div class="card">
        <h3>Detail Kelompok {{ $kelompok->nomor_kelompok }}</h3>

        <p><b>Desa:</b> {{ $kelompok->desa }}</p>
        <p><b>Dusun:</b> {{ $kelompok->dusun }}</p>
        <p><b>APL:</b> {{ optional($kelompok->apl)->nama }}</p>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Gender</th>
                </tr>
            </thead>

            <tbody>
                @forelse($kelompok->peserta as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->nim }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->prodi }}</td>
                        <td>{{ $p->gender }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada peserta</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection