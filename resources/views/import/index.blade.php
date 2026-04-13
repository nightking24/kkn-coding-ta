@extends('layouts.app')

@section('content')

    <div style="display:flex; justify-content:center; margin-top:50px;">

        <div style="
                width:100%;
                max-width:500px;
                background:white;
                padding:30px;
                border-radius:12px;
                box-shadow:0 4px 12px rgba(0,0,0,0.1);
                text-align:center;
            ">

            <h2 style="margin-bottom:20px;">Import Data Peserta</h2>

            @if ($errors->any())
                <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:6px; margin-bottom:15px;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div style="background:#d4edda; color:#155724; padding:10px; border-radius:6px; margin-bottom:15px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ url('/import/preview?periode_id=' . session('periode_id')) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="font-weight:bold;">Upload File CSV</label><br><br>

                    <input type="file" name="file" required
                        style="padding:8px; border:1px solid #ccc; border-radius:6px; width:100%;">
                </div>

                <button type="submit" style="
                        background:#007bff;
                        color:white;
                        border:none;
                        padding:10px 20px;
                        border-radius:6px;
                        cursor:pointer;
                    ">
                    Preview
                </button>

            </form>

        </div>

    </div>

@endsection