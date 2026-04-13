@extends('layouts.app')

@section('content')

    <div class="card">

        <h3>Selamat datang, <b>{{ session('user')->username }}</b></h3>

        <hr>

        @if($peserta && $peserta->kelompok)

            <h2>Kelompok {{ $peserta->kelompok->nomor_kelompok }}</h2>

            <p><b>Desa:</b> {{ $peserta->kelompok->desa }}</p>
            <p><b>Dusun:</b> {{ $peserta->kelompok->dusun }}</p>

            <br>

            <p><b>DPL:</b> {{ optional($peserta->kelompok->dpl)->nama }}</p>
            <p><b>No HP:</b> {{ optional($peserta->kelompok->dpl)->no_telp }}</p>

            <br>

            <p><b>APL:</b> {{ optional($peserta->kelompok->apl)->nama }}</p>
            <p><b>No HP:</b> {{ optional($peserta->kelompok->apl)->no_telp }}</p>

        @else
            <p>Belum mendapatkan kelompok</p>
        @endif

    </div>

    <h3>Anggota Kelompok</h3>

    @if($peserta && $peserta->kelompok)

        <table width="100%" border="1" cellpadding="8" cellspacing="0">
            <thead style="background:#343a40; color:white;">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peserta->kelompok->peserta as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->nim }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->prodi }}</td>
                        <td>{{ $p->gender }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

@endsection