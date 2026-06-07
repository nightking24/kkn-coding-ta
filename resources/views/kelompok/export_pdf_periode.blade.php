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
            max-width: 80px;
            white-space: normal;
            line-height: 1.1;
        }

        td {
            height: auto;
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
        }

        @media print {
            body {
                margin: 0;
                padding: 5px;
            }
            table {
                page-break-inside: avoid;
            }
            tr {
                page-break-inside: avoid;
                min-height: 18px;
            }
            td[rowspan] {
                vertical-align: middle;
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
                        <td>
                            {{ $p->nama }}
                        </td>

                        {{-- PRODI --}}
                        <td>
                            {{ $p->prodi }}
                        </td>

                        {{-- GENDER --}}
                        <td>
                            {{ in_array($p->gender, ['L', 'Pria']) ? 'Laki-Laki' : 'Perempuan' }}
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