@extends('layouts.app')

@section('content')

    <div class="card">

        <h3>Selamat datang, <b>{{ session('user')->username }}</b></h3>

        <hr>

        <h4>Kelompok yang Anda dampingi</h4>

        <div class="table-wrapper">
            <table id="table-kelompok" class="display">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kelompok</th>
                        <th>Desa</th>
                        <th>Dusun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelompok as $i => $k)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $k->nomor_kelompok }}</td>
                            <td>{{ $k->desa }}</td>
                            <td>{{ $k->dusun }}</td>
                            <td>
                                <a href="/hasil-apl/detail/{{ $k->id_kelompok }}?periode_id={{ session('periode_id') }}" class="btn btn-blue">
                                    Lihat
                                </a>
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
        $(document).ready(function () {
            $('#table-kelompok').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25],
            });
        });
    </script>
@endsection