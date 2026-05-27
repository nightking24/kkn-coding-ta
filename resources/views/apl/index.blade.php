@extends('layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h2>Data APL</h2>
            <a href="/apl/create" class="btn btn-green">+ Tambah</a>
        </div>

        @if(session('error'))
            <div style="
                background:#f8d7da;
                color:#721c24;
                padding:12px;
                border-radius:8px;
                margin-bottom:15px;
                border-left:5px solid #dc3545;
            ">
                {{ session('error') }}
            </div>
        @endif

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
            <table id="aplTable" class="display">
                <thead style="background: #343a40; color: white;">
                    <tr>
                        <th style="text-align: center; padding: 12px;">No</th>
                        <th style="text-align: center; padding: 12px;">NIM</th>
                        <th style="text-align: center; padding: 12px;">Nama</th>
                        <th style="text-align: center; padding: 12px;">Email</th>
                        <th style="text-align: center; padding: 12px;">No Telp</th>
                        <th style="text-align: center; padding: 12px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="text-align: center; padding: 12px;">{{ $loop->iteration }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->nim }}</td>
                            <td style="text-align: left; padding: 12px;">{{ $d->nama }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->email }}</td>
                            <td style="text-align: center; padding: 12px;">{{ $d->no_telp }}</td>
                            <td style="text-align: center; padding: 12px;">
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