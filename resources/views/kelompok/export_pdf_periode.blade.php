<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kelompok KKN</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            padding: 5px;
            line-height: 1.2;
        }

        h2 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 12px;
            margin-top: 0;
        }

        .header {
            margin-bottom: 8px;
            font-size: 8px;
        }

        .header p {
            margin-bottom: 2px;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: avoid;
            font-size: 7px;
            table-layout: fixed;
        }

        tr {
            page-break-inside: avoid;
            height: auto;
            min-height: 18px;
        }

        th,
        td {
            border: 0.5px solid #333;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-size: 7px;
            white-space: normal;
            line-height: 1.1;
        }

        /* Kolom Kelompok */
        th:nth-child(1),
        td:nth-child(1) {
            width: 4.5%;
        }

        /* Kolom No */
        th:nth-child(2),
        td:nth-child(2) {
            width: 3.5%;
        }

        /* Kolom NIM */
        th:nth-child(3),
        td:nth-child(3) {
            width: 8%;
        }

        /* Kolom Nama Lengkap */
        th:nth-child(4) {
            width: 17%;
            word-break: break-word;
            text-align: center;
        }

        td:nth-child(4) {
            width: 17%;
            word-break: break-word;
            text-align: left !important;
        }

        /* Class untuk Nama Lengkap */
        td.nama-lengkap {
            text-align: left !important;
        }

        tbody tr td.nama-lengkap {
            text-align: left !important;
        }

        /* Kolom Prodi */
        tbody tr td.prodi-col {
            text-align: left !important;
        }

        /* Kolom Gender */
        tbody tr td.gender-col {
            text-align: left !important;
        }

        /* Kolom Prodi */
        th:nth-child(5),
        td:nth-child(5) {
            width: 9%;
        }

        /* Kolom Gender */
        th:nth-child(6),
        td:nth-child(6) {
            width: 5%;
        }

        /* Kolom DPL */
        th:nth-child(7),
        td:nth-child(7) {
            width: 10%;
            word-break: break-word;
        }

        /* Kolom Kontak DPL */
        th:nth-child(8),
        td:nth-child(8) {
            width: 9%;
        }

        /* Kolom APL */
        th:nth-child(9),
        td:nth-child(9) {
            width: 10%;
            word-break: break-word;
        }

        /* Kolom Kontak APL */
        th:nth-child(10),
        td:nth-child(10) {
            width: 9%;
        }

        /* Kolom Desa */
        th:nth-child(11),
        td:nth-child(11) {
            width: 7%;
        }

        /* Kolom Dusun */
        th:nth-child(12),
        td:nth-child(12) {
            width: 7%;
        }

        /* Styling untuk kolom dengan rowspan */
        td[rowspan] {
            vertical-align: middle;
            padding: 3px 1px;
            font-weight: 500;
        }

        thead tr {
            background-color: #b7dee8;
        }

        thead th {
            background-color: #b7dee8;
            font-weight: bold;
            font-size: 6.5px;
            padding: 2px 1px;
            word-break: break-word;
        }

        @media print {
            body {
                margin: 0;
                padding: 5px;
            }

            table {
                page-break-inside: avoid;
                table-layout: fixed;
            }

            tr {
                page-break-inside: avoid;
                min-height: 18px;
            }

            td[rowspan] {
                vertical-align: middle;
            }

            td.nama-lengkap {
                text-align: left !important;
            }

            td.prodi-col {
                text-align: left !important;
            }

            td.gender-col {
                text-align: left !important;
            }

            th:nth-child(1),
            td:nth-child(1) {
                width: 4.5%;
            }

            th:nth-child(2),
            td:nth-child(2) {
                width: 3.5%;
            }

            th:nth-child(3),
            td:nth-child(3) {
                width: 8%;
            }

            th:nth-child(4) {
                width: 17%;
                text-align: center;
            }

            td:nth-child(4) {
                width: 17%;
                text-align: left !important;
            }

            th:nth-child(5),
            td:nth-child(5) {
                width: 9%;
            }

            th:nth-child(6),
            td:nth-child(6) {
                width: 5%;
            }

            th:nth-child(7),
            td:nth-child(7) {
                width: 10%;
            }

            th:nth-child(8),
            td:nth-child(8) {
                width: 9%;
            }

            th:nth-child(9),
            td:nth-child(9) {
                width: 10%;
            }

            th:nth-child(10),
            td:nth-child(10) {
                width: 9%;
            }

            th:nth-child(11),
            td:nth-child(11) {
                width: 7%;
            }

            th:nth-child(12),
            td:nth-child(12) {
                width: 7%;
            }
        }
    </style>
</head>

<body>

    <h2>LAPORAN KELOMPOK KKN REGULER</h2>

    <div class="header">
        <p><b>Nama KKN:</b> {{ $periode->nama_kkn }}</p>
        <p><b>Tahun:</b> {{ $periode->tahun_kkn }}</p>
        <p><b>Lokasi:</b> {{ $periode->lokasi }}</p>
    </div>

    @foreach($kelompok as $k)

        <table>

            <thead>
                <tr>
                    <th>Kelompok</th>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Prodi</th>
                    <th>Gender</th>
                    <th>DPL</th>
                    <th>Kontak DPL</th>
                    <th>APL</th>
                    <th>Kontak APL</th>
                    <th>Desa</th>
                    <th>Dusun</th>
                </tr>
            </thead>

            <tbody>

                @foreach($k->peserta as $p)

                    <tr>

                        {{-- KELOMPOK --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ $k->nomor_kelompok }}
                            </td>
                        @endif

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        {{-- NIM --}}
                        <td>
                            {{ $p->nim }}
                        </td>

                        {{-- NAMA --}}
                        <td class="nama-lengkap" style="text-align: left;">
                            {{ $p->nama }}
                        </td>

                        {{-- PRODI --}}
                        <td class="prodi-col" style="text-align: left;">
                            {{ optional($p->prodiRel)->nama_prodi ?? '-' }}
                        </td>

                        {{-- GENDER --}}
                        <td class="gender-col" style="text-align: left;">
                            {{ in_array($p->gender, ['L', 'Pria']) ? 'Pria' : 'Wanita' }}
                        </td>

                        {{-- DPL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ optional($k->dpl)->nama ?? '-' }}
                            </td>
                        @endif

                        {{-- KONTAK DPL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ optional($k->dpl)->no_telp ?? '-' }}
                            </td>
                        @endif

                        {{-- APL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ optional($k->apl)->nama ?? '-' }}
                            </td>
                        @endif

                        {{-- KONTAK APL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ optional($k->apl)->no_telp ?? '-' }}
                            </td>
                        @endif

                        {{-- DESA --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ $k->desa }}
                            </td>
                        @endif

                        {{-- DUSUN --}}
                        @if($loop->first)
                            <td rowspan="{{ count($k->peserta) }}">
                                {{ $k->dusun }}
                            </td>
                        @endif

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endforeach

</body>

</html>