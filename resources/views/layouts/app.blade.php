<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <title>Pembagian Kelompok KKN Reguler</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .wrapper {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #1e7e34;
            color: white;
            min-height: 100vh;
            padding: 0;
        }

        .sidebar h3 {
            margin-bottom: 0;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu li {
            margin-bottom: 0;
        }

        .menu a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            border-radius: 0;
        }

        .menu a:hover {
            background: #0f5f37;
        }

        .submenu {
            background-color: #0a1a0e;
            margin-left: 0;
            font-size: 14px;
            padding: 0;
            margin: 0;
        }

        .submenu a {
            padding: 10px 20px 10px 40px !important;
            color: #d4edda !important;
            font-size: 14px;
        }

        .submenu a:hover {
            background-color: #050d08 !important;
            color: #ffffff !important;
        }

        .submenu a.active {
            background-color: #050d08 !important;
            color: #ffffff !important;
            font-weight: 500;
        }

        .content {
            flex: 1;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 1200px;
        }

        .card {
            width: 100%;
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 30px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
        }

        .btn-green {
            background: #1e7e34;
        }
        
        .btn-green:hover {
            background: #0f5f37;
        }

        .btn-blue {
            background: #1e7e34;
        }

        .btn-red {
            background: #dc3545;
        }

        .btn-gray {
            background: #6c757d;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
        }

        table.dataTable {
            width: 100% !important;
        }

        th {
            background: #343a40;
            color: white;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        .col-nama {
            width: 150px;
        }

        .col-alamat {
            width: 250px;
        }

        table {
            width: 100%;
        }

        td {
            white-space: nowrap;
        }

        .col-wrap {
            white-space: normal;
            line-height: 1.4;
            max-width: 200px;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: none !important;
            border: none !important;
            color: #007bff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #007bff !important;
            color: white !important;
            border-radius: 5px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #0056b3 !important;
            color: white !important;
        }

        .active {
            background: #1abc9c !important;
            font-weight: bold;
        }

        .menu a {
            transition: 0.3s;
        }

        select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.3);
        }
        
    </style>

</head>

<body>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#table-berhasil').DataTable();
            $('#table-gagal').DataTable();
        });
    </script>

    <div class="wrapper">

        <div class="sidebar">

            @if(session('user')->role == 'admin')
                @include('layouts.sidebar_admin')
            @elseif(session('user')->role == 'peserta')
                @include('layouts.sidebar_peserta')
            @elseif(session('user')->role == 'dpl')
                @include('layouts.sidebar_dpl')
            @elseif(session('user')->role == 'apl')
                @include('layouts.sidebar_apl')
            @endif

        </div>

        <div class="content">
            <div class="container">
                @yield('content')
            </div>
        </div>

    </div>

    @yield('scripts')

</body>

</html>