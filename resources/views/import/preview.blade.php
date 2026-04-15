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
                <table style="
                                            width:100%;
                                            border-collapse:collapse;
                                            text-align:center;
                                        ">
                    <thead style="background:#343a40; color:white;">
                        <tr>
                            <th style="padding:10px;">Nama</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Gender</th>
                            <th>Bahasa Jawa</th>
                            <th>Riwayat</th>
                            <th>Khusus</th>
                            <th>Detail Penyakit</th>
                            <th>Detail Khusus</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($preview as $p)
                            <tr style="border-bottom:1px solid #ddd;">
                                <td>{{ $p['nama'] }}</td>
                                <td>{{ $p['nim'] }}</td>
                                <td>{{ $p['prodi'] }}</td>
                                <td>{{ $p['gender'] }}</td>
                                <td>{{ $p['bahasa_jawa'] }}</td>
                                <td>{{ $p['riwayat_penyakit'] }}</td>
                                <td>{{ $p['berkebutuhan_khusus'] }}</td>
                                <td>{{ $p['detail_penyakit'] }}</td>
                                <td>{{ $p['detail_khusus'] }}</td>
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
                            <input type="hidden" name="data" value="{{ json_encode($preview) }}">
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