@extends('layouts.app')

@section('content')

    <div class="card">
        <p>Selamat datang, <b>{{ session('user')->username }}</b></p>

        <h3>Anda membimbing:</h3>

        <div class="table-wrapper">
            <table id="table-dpl" class="display">
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
                    @forelse($kelompok as $i => $k)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $k->nomor_kelompok }}</td>
                            <td>{{ $k->desa }}</td>
                            <td>{{ $k->dusun }}</td>
                            <td>
                                <a href="/hasil-dpl/detail/{{ $k->id_kelompok }}" class="btn btn-blue">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Tidak ada kelompok</td>
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
            $('#table-dpl').DataTable({
                pageLength: 5
            });
        });
    </script>
@endsection