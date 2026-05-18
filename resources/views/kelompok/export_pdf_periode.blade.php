<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kelompok KKN</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        .header {
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