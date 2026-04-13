@extends('layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h2>Data APL</h2>
            <a href="/apl/create" class="btn btn-green">+ Tambah</a>
        </div>

        @if ($errors->any())
            <div style="
                background:#f8d7da;
                color:#721c24;
                padding:12px;
                border-radius:8px;
                margin-bottom:15px;
                border-left:5px solid #dc3545;
            ">
                <b>Terjadi kesalahan:</b>
                <ul style="margin:5px 0 0 15px;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div style="
                        background:#d4edda;
                        color:#155724;
                        padding:12px;
                        border-radius:8px;
                        margin-bottom:15px;
                        border-left:5px solid #28a745;
                    ">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-wrapper">
            <table id="aplTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No Telp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->nim }}</td>
                            <td>{{ $d->nama }}</td>
                            <td>{{ $d->email }}</td>
                            <td>{{ $d->no_telp }}</td>
                            <td>
                                <a href="/apl/edit/{{ $d->nim }}" class="btn btn-blue">Edit</a>
                                <a href="/apl/delete/{{ $d->nim }}" class="btn btn-red">Hapus</a>
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
        $('#aplTable').DataTable({
            pageLength: 5,
            language: {
                search: "🔍 Cari:"
            }
        });
    </script>
@endsection