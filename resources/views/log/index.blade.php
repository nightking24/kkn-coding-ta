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
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $log->created_at }}</td>
                        <td>{{ $log->username }}</td>
                        <td>{{ $log->aktivitas }}</td>
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