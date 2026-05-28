@extends('layouts.app')

@section('content')

    <div style="padding:20px;">

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

        <div style="
                                                        background:white;
                                                        padding:25px;
                                                        border-radius:12px;
                                                        box-shadow:0 4px 12px rgba(0,0,0,0.1);
                                                    ">

            <h2 style="margin-bottom:20px;">Preview Data Peserta</h2>

            @if (!empty($errors))
                <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:6px; margin-bottom:15px;">
                    <b>Data bermasalah:</b>
                    <ul>
                        @foreach ($errors as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="overflow-x:auto;">
                <table id="table-preview" class="display">
                    <thead style="background:#343a40; color:white;">
                        <tr>
                            <th style="text-align: center; padding: 12px;">Nama</th>
                            <th style="text-align: center; padding: 12px;">NIM</th>
                            <th style="text-align: center; padding: 12px;">Prodi</th>
                            <th style="text-align: center; padding: 12px;">Gender</th>
                            <th style="text-align: center; padding: 12px;">No Telp</th>
                            <th style="text-align: center; padding: 12px;">Bahasa Jawa</th>
                            <th style="text-align: center; padding: 12px;">Riwayat</th>
                            <th style="text-align: center; padding: 12px;">Khusus</th>
                            <th style="text-align: center; padding: 12px;">Detail Penyakit</th>
                            <th style="text-align: center; padding: 12px;">Detail Khusus</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($preview as $p)
                            <tr style="border-bottom:1px solid #ddd;">
                                <td style="text-align: left; padding: 12px;">{{ $p['nama'] }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['nim'] }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['prodi'] }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['gender'] }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['no_telp'] }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['bahasa_jawa'] == 1 ? 'Bisa' : 'Tidak' }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['riwayat_penyakit'] == 1 ? 'Ya' : 'Tidak' }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['berkebutuhan_khusus'] == 1 ? 'Ya' : 'Tidak' }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['detail_penyakit'] }}</td>
                                <td style="text-align: center; padding: 12px;">{{ $p['detail_khusus'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <br>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">

                <a href="{{ url('/import?periode_id=' . session('periode_id')) }}" class="btn btn-secondary">
                    ← Kembali
                </a>

                @if (empty($errors))
                    <div style="display:flex; gap:10px;">

                        <form action="{{ url('/generate?periode_id=' . session('periode_id')) }}" method="POST">
                            @csrf
                            <input type="hidden" name="data" value='@json($preview)'>
                            <button type="submit" class="btn btn-success">
                                Generate
                            </button>
                        </form>
                    </div>
                @endif

            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#table-preview').DataTable({
                scrollX: true
            });
        });
    </script>
@endsection