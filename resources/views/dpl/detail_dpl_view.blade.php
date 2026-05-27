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

        #table-peserta {
            width: 100% !important;
        }

        body {
            overflow-x: hidden;
        }
    </style>

    <div class="detail-card">

        {{-- BUTTON KEMBALI --}}
        <div style="margin-bottom: 15px;">
            <a href="/dpl-view" class="btn btn-secondary">
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

                {{-- APL --}}
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
                        👨‍🏫 Asisten Pembimbing Lapangan (APL)
                    </h5>

                    <p style="
                                font-size: 14px;
                                margin-bottom: 8px;
                            ">
                        <b>APL:</b>
                        {{ optional($kelompok->apl)->nama }}
                    </p>

                    <p style="
                                font-size: 14px;
                                margin-bottom: 0;
                            ">
                        <b>No Telp:</b>
                        {{ optional($kelompok->apl)->no_telp }}
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

                    <table id="table-peserta" class="table"
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

                                    <td style="text-align:center;">
                                        {{ $p->prodi }}
                                    </td>

                                    <td style="text-align:center;">

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

    <style>
        /* =========================
           TABLE STYLE
        ========================== */

        #table-peserta {
            border-collapse: collapse !important;
        }

        /* Header */
        #table-peserta thead th {
            border-bottom: 1px solid #d9d9d9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            font-weight: 600;
        }

        /* Body Row */
        #table-peserta tbody tr {
            border-bottom: 1px solid #ececec !important;
        }

        /* Cell */
        #table-peserta tbody td {
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #ececec !important;
            vertical-align: middle;
        }

        /* Hilangkan garis hitam DataTables */
        table.dataTable.no-footer {
            border-bottom: 1px solid #ececec !important;
        }

        /* Hover */
        #table-peserta tbody tr:hover {
            background: #fafafa !important;
        }

        /* Pagination */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none !important;
        }
    </style>

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {

            $('#table-peserta').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                scrollX: true
            });

        });
    </script>

@endsection