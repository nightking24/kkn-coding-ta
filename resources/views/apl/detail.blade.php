@extends('layouts.app')

@section('content')

    <div class="card">

        <h2 style="margin-bottom:10px;">
            Detail Kelompok {{ $kelompok->nomor_kelompok }}
        </h2>

        <hr>

        <div style="margin-bottom:20px;">
            <p><b>Desa:</b> {{ $kelompok->desa }}</p>
            <p><b>Dusun:</b> {{ $kelompok->dusun }}</p>
            <p><b>DPL:</b> {{ optional($kelompok->dpl)->nama }}</p>
        </div>

        <h4>Anggota Kelompok</h4>

        <div class="table-wrapper">
            <table id="table-anggota" class="display">
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
                    @foreach($kelompok->peserta as $i => $p)
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
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#table-anggota').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25],
            });
        });
    </script>
@endsection