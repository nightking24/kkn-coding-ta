<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Hasil Pembagian Kelompok KKN</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        thead th {
            background-color: #b7dee8;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>HASIL PEMBAGIAN KELOMPOK KKN REGULER</h2>

    @foreach($grouped as $nomorKelompok => $items)

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
                    <th>Kecamatan</th>
                    <th>Desa</th>
                    <th>Dusun</th>
                </tr>
            </thead>

            <tbody>

                @foreach($items as $p)

                    <tr>

                        {{-- KELOMPOK --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ $nomorKelompok }}
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
                            <td rowspan="{{ count($items) }}">
                                {{ optional($p->kelompok->dpl)->nama ?? '-' }}
                            </td>
                        @endif

                        {{-- KONTAK DPL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ optional($p->kelompok->dpl)->no_telp ?? '-' }}
                            </td>
                        @endif

                        {{-- APL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ optional($p->kelompok->apl)->nama ?? '-' }}
                            </td>
                        @endif

                        {{-- KONTAK APL --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ optional($p->kelompok->apl)->no_telp ?? '-' }}
                            </td>
                        @endif

                        {{-- KECAMATAN --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ $p->kelompok->nama_kecamatan ?? '-' }}
                            </td>
                        @endif

                        {{-- DESA --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ $p->kelompok->desa ?? '-' }}
                            </td>
                        @endif

                        {{-- DUSUN --}}
                        @if($loop->first)
                            <td rowspan="{{ count($items) }}">
                                {{ $p->kelompok->dusun ?? '-' }}
                            </td>
                        @endif

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endforeach

</body>

</html>