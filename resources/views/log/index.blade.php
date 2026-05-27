@extends('layouts.app')

@section('content')

    <div class="card">
        <h2 style="margin-bottom:20px;">Log Aktivitas</h2>

        <!-- FILTER TANGGAL -->
        <div style="margin-bottom:15px;">
            <form method="GET" action="">
                Dari:
                <input type="date" name="start_date" value="{{ request('start_date') }}">

                Sampai:
                <input type="date" name="end_date" value="{{ request('end_date') }}">

                <button class="btn btn-blue">Filter</button>
                <a href="/log-aktivitas" class="btn btn-gray">Reset</a>
            </form>
        </div>

        <table id="logTable" class="display">
            <thead style="background: #343a40; color: white;">
                <tr>
                    <th style="text-align: center; padding: 12px;">No</th>
                    <th style="text-align: center; padding: 12px;">Waktu</th>
                    <th style="text-align: center; padding: 12px;">User</th>
                    <th style="padding: 12px;">Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;">{{ $i + 1 }}</td>
                        <td style="padding: 12px;">{{ $log->created_at }}</td>
                        <td style="padding: 12px;">{{ $log->username }}</td>
                        <td style="text-align: left; padding: 12px;">{{ $log->aktivitas }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#logTable').DataTable();
        });
    </script>
@endsection