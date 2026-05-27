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
            <a href="{{ url('/periode/' . $periode->id_periode . '/export-excel?periode_id=' . session('periode_id')) }}"
                class="btn btn-green">
                Export Excel
            </a>

            <a href="{{ url('/periode/' . $periode->id_periode . '/export-pdf?periode_id=' . session('periode_id')) }}"
                class="btn btn-red">
                Export PDF
            </a>
        </div>

    </div>

    <div class="card">

        <h3>Daftar Kelompok</h3>

        <div style="overflow-x: auto;">
            <table id="table-kelompok" class="display">
                <thead>
                    <tr style="background:#343a40; color:white;">
                        <th style="text-align: center; padding: 12px;">No</th>
                        <th style="text-align: center; padding: 12px;">Kelompok</th>
                        <th style="text-align: center; padding: 12px;">Desa</th>
                        <th style="text-align: center; padding: 12px;">Dusun</th>
                        <th style="text-align: center; padding: 12px;">DPL</th>
                        <th style="text-align: center; padding: 12px;">APL</th>
                        <th style="text-align: center; padding: 12px;">Jumlah Peserta</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($kelompok as $i => $k)
                        <tr>
                            <td style="text-align: center; padding: 12px;">{{ $i + 1 }}</td>
                            <td style="text-align: center; padding: 12px;">K{{ $k->nomor_kelompok }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $k->desa }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $k->dusun }}</td>
                            <td style="text-align: left; padding: 12px;">{{ optional($k->dpl)->nama ?? '-' }}</td>
                            <td style="text-align: left; padding: 12px;">{{ optional($k->apl)->nama ?? '-' }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $k->peserta->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" align="center">Belum ada data kelompok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#table-kelompok').DataTable({
                scrollX: true
            });
        });
    </script>
@endsection