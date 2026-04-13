@extends('layouts.app')

@section('content')

    <div class="card">

        <h3>Data Kelompok DPL</h3>

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
                    @forelse($kelompok as $i => $k)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $k->nomor_kelompok }}</td>
                            <td>{{ $k->desa }}</td>
                            <td>{{ $k->dusun }}</td>
                            <td>
                                <a href="/hasil-dpl/detail/{{ $k->id_kelompok }}?periode_id={{ session('periode_id') }}" class="btn btn-blue">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Data tidak ada</td>
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
                pageLength: 5,
                lengthMenu: [5, 10, 25],
            });
        });
    </script>
@endsection