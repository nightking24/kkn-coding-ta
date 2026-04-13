@extends('layouts.app')

@section('content')

    <div class="card">

        <div class="card-header">
            <h2>Data Periode KKN</h2>
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

            <a href="/periode/create" class="btn btn-green">+ Tambah Periode</a>
        </div>

        <div class="table-wrapper">
            <table id="periodeTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tahun</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($periode as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $p->nama_kkn }}</td>
                            <td>{{ $p->tahun_kkn }}</td>
                            <td>{{ $p->lokasi }}</td>
                            <td>
                                @if($p->status == 1)
                                    <span style="color:green; font-weight:bold;">Aktif</span>
                                @else
                                    <span style="color:red; font-weight:bold;">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:8px; justify-content:center;">
                                    <a href="/periode/edit/{{ $p->id_periode }}" class="btn btn-blue">Edit</a>
                                    <a href="/periode/delete/{{ $p->id_periode }}" class="btn btn-red">Hapus</a>
                                </div>
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
            $('#periodeTable').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        next: "Next",
                        previous: "Prev"
                    }
                }
            });
        });
    </script>
@endsection