@extends('layouts.app')

@section('content')

    <div class="card">

        <h2>{{ $periode->nama_kkn }} ({{ $periode->tahun_kkn }})</h2>

        <p><b>Lokasi:</b> {{ $periode->lokasi }}</p>
        <p><b>Tanggal:</b> {{ $periode->tanggal_mulai }} - {{ $periode->tanggal_selesai }}</p>
        <p><b>Total Kelompok:</b> {{ $total_kelompok }}</p>
        <p><b>Total Peserta:</b> {{ $total_peserta }}</p>

    </div>

    <div style="margin:20px 0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">

        <div>
            <a href="/dashboard" class="btn btn-gray">← Kembali</a>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="{{ url('/periode/' . $periode->id_periode . '/export-pdf?periode_id=' . session('periode_id')) }}"
                class="btn btn-red">
                Export PDF
            </a>

            <a href="{{ url('/periode/' . $periode->id_periode . '/export-excel?periode_id=' . session('periode_id')) }}"
                class="btn btn-green">
                Export Excel
            </a>
        </div>

    </div>

    <div class="card">

        <h3>Daftar Kelompok</h3>

        <table width="100%" border="1" cellpadding="8" cellspacing="0">
            <tr style="background:#343a40; color:white;">
                <th>No</th>
                <th>Kelompok</th>
                <th>Desa</th>
                <th>Dusun</th>
                <th>DPL</th>
                <th>APL</th>
                <th>Jumlah Peserta</th>
            </tr>

            <tbody>
                @forelse($kelompok as $i => $k)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>K{{ $k->nomor_kelompok }}</td>
                        <td>{{ $k->desa }}</td>
                        <td>{{ $k->dusun }}</td>
                        <td>{{ optional($k->dpl)->nama ?? '-' }}</td>
                        <td>{{ optional($k->apl)->nama ?? '-' }}</td>
                        <td>{{ $k->peserta->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" align="center">Belum ada data kelompok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection