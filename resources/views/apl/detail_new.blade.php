@extends('layouts.app')

@section('content')

    <style>
        .detail-card {
            max-width: 1200px;
            margin: auto;
        }

        .detail-table-wrapper {
            overflow-x: auto;
        }

        #table-anggota {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        body {
            overflow-x: hidden;
        }

        /* =========================
                   TABLE STYLE
                ========================== */

        #table-anggota thead th {
            border-bottom: 1px solid #d9d9d9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            font-weight: 600;
        }

        #table-anggota tbody tr {
            border-bottom: 1px solid #ececec !important;
        }

        #table-anggota tbody td {
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #ececec !important;
            vertical-align: middle;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #ececec !important;
        }

        #table-anggota tbody tr:hover {
            background: #fafafa !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none !important;
        }
    </style>

    <div class="detail-card">

        {{-- BUTTON KEMBALI --}}
        <div style="margin-bottom: 15px;">
            <a href="{{ url('/hasil-apl-new') }}" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>

        {{-- CARD DETAIL --}}
        <div class="card" style="padding: 20px;">

            {{-- HEADER KELOMPOK --}}
            <div style="
                            background: linear-gradient(135deg, #1e7e34 0%, #0f5f37 100%);
                            color: white;
                            padding: 16px 20px;
                            border-radius: 10px;
                            margin-bottom: 22px;
                        ">

                <h2 style="
                                margin: 0 0 15px 0;
                                font-size: 20px;
                            ">
                    Kelompok {{ $kelompok->nomor_kelompok }}
                </h2>

                <p style="
                                margin: 0 0 8px 0;
                                font-size: 14px;
                                opacity: 0.9;
                            ">
                    Periode:
                    {{ optional($kelompok->periode)->tahun_kkn ?? '-' }}
                </p>

                <p style="
                                margin: 0;
                                font-size: 14px;
                                opacity: 0.9;
                            ">
                    Lokasi:
                    {{ optional($kelompok->periode)->lokasi }}
                </p>

            </div>

            {{-- DETAIL --}}
            <div style="
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 18px;
                            margin-bottom: 8px;
                        ">

                {{-- DETAIL LOKASI --}}
                <div style="
                                border-left: 4px solid #1e7e34;
                                padding-left: 12px;
                            ">

                    <h5 style="
                                    color: #1e7e34;
                                    margin-bottom: 10px;
                                    font-size: 16px;
                                    font-weight: 600;
                                ">
                        📍 Detail Lokasi
                    </h5>

                    <p style="
                                    font-size: 14px;
                                    margin-bottom: 8px;
                                ">
                        <b>Kecamatan:</b>
                        {{ $kelompok->nama_kecamatan }}
                    </p>

                    <p style="
                                    font-size: 14px;
                                    margin-bottom: 8px;
                                ">
                        <b>Desa:</b>
                        {{ $kelompok->desa }}
                    </p>

                    <p style="
                                    font-size: 14px;
                                    margin-bottom: 0;
                                ">
                        <b>Dusun:</b>
                        {{ $kelompok->dusun }}
                    </p>

                </div>

                {{-- DPL --}}
                <div style="
                                border-left: 4px solid #1e7e34;
                                padding-left: 12px;
                            ">

                    <h5 style="
                                    color: #1e7e34;
                                    margin-bottom: 10px;
                                    font-size: 16px;
                                    font-weight: 600;
                                ">
                        👨‍🏫 Dosen Pembimbing Lapangan (DPL)
                    </h5>

                    <p style="
                                    font-size: 14px;
                                    margin-bottom: 8px;
                                ">
                        <b>DPL:</b>
                        {{ optional($kelompok->dpl)->nama }}
                    </p>

                    <p style="
                                    font-size: 14px;
                                    margin-bottom: 0;
                                ">
                        <b>No Telp:</b>
                        {{ optional($kelompok->dpl)->no_telp }}
                    </p>

                </div>

            </div>

        </div>

        {{-- CARD TABEL --}}
        <div style="margin-top: 18px;">

            <div class="card" style="padding: 20px;">

                <h3 style="
                                margin-bottom: 20px;
                                color: #1e7e34;
                                border-bottom: 3px solid #1e7e34;
                                padding-bottom: 10px;
                            ">
                    👥 Anggota Kelompok
                </h3>

                <div class="detail-table-wrapper">

                    <table id="table-anggota" class="table" style="
                                    margin-bottom: 0;
                                    font-size: 13px;
                                ">

                        <thead style="
                                        background: #343a40;
                                        color: white;
                                    ">

                            <tr>
                                <th style="text-align:center;">No</th>
                                <th style="text-align:center;">NIM</th>
                                <th style="text-align:center;">Nama</th>
                                <th style="text-align:center;">Prodi</th>
                                <th style="text-align:center;">Gender</th>
                                <th style="text-align:center;">No Telp</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($kelompok->peserta as $i => $p)

                                <tr>

                                    <td style="text-align:center;">
                                        {{ $i + 1 }}
                                    </td>

                                    <td style="text-align:center;">
                                        {{ $p->nim }}
                                    </td>

                                    <td style="text-align:left;">
                                        {{ $p->nama }}
                                    </td>

                                    <td style="text-align:left;">
                                        {{ optional($p->prodiRel)->nama_prodi ?? '-' }}
                                    </td>

                                    <td style="text-align:left;">

                                        @if(
                                                $p->gender == 'L' ||
                                                $p->gender == 'Pria' ||
                                                $p->gender == 'Laki-Laki'
                                            )

                                            Laki-Laki

                                        @else

                                            Perempuan

                                        @endif

                                    </td>

                                    <td style="text-align:center;">
                                        {{ $p->no_telp ?? '-' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {

            $('#table-anggota').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                scrollX: true
            });

        });
    </script>

@endsection