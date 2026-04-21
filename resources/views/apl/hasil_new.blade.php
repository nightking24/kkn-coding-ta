@extends('layouts.app')

@section('content')

    <div class="card">
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 10px; color: #000;">Selamat datang, <b
                    style="color: #1e7e34;">{{ session('user')->username }}</b></h3>
            <p style="color: #666; font-size: 14px; margin: 0;">Portal Pembagian Kelompok KKN Reguler</p>
        </div>

        <h3 style="margin-bottom: 25px; color: #1e7e34; border-bottom: 3px solid #1e7e34; padding-bottom: 15px;">
            📋 Kelompok yang Anda Dampingi:
        </h3>

        <div class="table-wrapper">
            <table id="table-kelompok" class="table table-hover" style="margin-bottom: 0;">
                <thead style="background: #343a40; color: white;">
                    <tr>
                        <th style="text-align: center; padding: 12px;">No</th>
                        <th style="padding: 12px;">Kelompok</th>
                        <th style="padding: 12px;">Desa</th>
                        <th style="padding: 12px;">Dusun</th>
                        <th style="text-align: center; padding: 12px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelompok as $i => $k)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="text-align: center; padding: 12px;">{{ $i + 1 }}</td>
                            <td style="padding: 12px; font-weight: 500;">Kelompok {{ $k->nomor_kelompok }}</td>
                            <td style="padding: 12px;">{{ $k->desa }}</td>
                            <td style="padding: 12px;">{{ $k->dusun }}</td>
                            <td style="text-align: center; padding: 12px;">
                                <a href="{{ url('/hasil-apl-new/detail/' . $k->id_kelompok) }}" class="btn btn-sm" style="background: #1e7e34; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none;">
                                    Detail
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