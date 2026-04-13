<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan KKN</title>
    <style>
        body {
            font-family: Arial;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        .header {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <h2>LAPORAN KELOMPOK KKN</h2>

    <div class="header">
        <p><b>Nama KKN:</b> {{ $periode->nama_kkn }}</p>
        <p><b>Tahun:</b> {{ $periode->tahun_kkn }}</p>
        <p><b>Lokasi:</b> {{ $periode->lokasi }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kelompok</th>
                <th>Desa</th>
                <th>Dusun</th>
                <th>DPL</th>
                <th>No Telp DPL</th>
                <th>APL</th>
                <th>No Telp APL</th>
                <th>Jumlah Peserta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelompok as $i => $k)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>K{{ $k->nomor_kelompok }}</td>
                    <td>{{ $k->desa }}</td>
                    <td>{{ $k->dusun }}</td>
                    <td>{{ optional($k->dpl)->nama ?? '-' }}</td>
                    <td>{{ optional($k->dpl)->no_telp ?? '-' }}</td>
                    <td>{{ optional($k->apl)->nama ?? '-' }}</td>
                    <td>{{ optional($k->apl)->no_telp ?? '-' }}</td>
                    <td>{{ $k->peserta->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>