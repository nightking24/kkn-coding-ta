<!DOCTYPE html>
<html>

<head>
    <title>Hasil Pembagian KKN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        th,
        td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }

        body {
            font-family: Arial;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #2c3e50;
            color: white;
            text-align: center;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        td {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>HASIL PEMBAGIAN KELOMPOK KKN</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kelompok</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Gender</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Dusun</th>
                <th>DPL</th>
                <th>No Telp DPL</th>
                <th>APL</th>
                <th>No Telp APL</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            @foreach($grouped as $kel => $items)

                <tr>
                    <td colspan="13" style="background:#ddd; font-weight:bold;">
                        Kelompok K{{ $kel }}
                    </td>
                </tr>

                @foreach($items as $p)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>K{{ $p->kelompok->nomor_kelompok ?? '-' }}</td>
                        <td>{{ $p->nim }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->prodi }}</td>
                        <td>{{ $p->gender }}</td>
                        <td>{{ optional($p->kelompok)->nama_kecamatan ?? '-' }}</td>
                        <td>{{ optional($p->kelompok)->desa ?? '-' }}</td>
                        <td>{{ optional($p->kelompok)->dusun ?? '-' }}</td>
                        <td>{{ optional($p->kelompok->dpl)->nama ?? '-' }}</td>
                        <td>{{ optional($p->kelompok->dpl)->no_telp ?? '-' }}</td>
                        <td>{{ optional($p->kelompok->apl)->nama ?? '-' }}</td>
                        <td>{{ optional($p->kelompok->apl)->no_telp ?? '-' }}</td>
                    </tr>
                @endforeach

            @endforeach
        </tbody>
    </table>

</html>