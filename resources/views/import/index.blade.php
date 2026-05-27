@extends('layouts.app')

@section('content')

    <div style="
                display:flex;
                justify-content:center;
                align-items:center;
                min-height:75vh;
                padding:20px;
            ">

        <div style="
                    width:100%;
                    max-width:700px;
                    background:white;
                    padding:45px;
                    border-radius:14px;
                    box-shadow:0 4px 15px rgba(0,0,0,0.12);
                ">

            {{-- TITLE --}}
            <div style="text-align:center; margin-bottom:35px;">

                <h2 style="
                            margin-bottom:10px;
                            font-size:42px;
                            font-weight:600;
                            color:#222;
                        ">
                    Import Data Peserta
                </h2>

                <p style="
                            color:#777;
                            margin:0;
                            font-size:15px;
                        ">
                    Upload file CSV peserta KKN untuk di preview sebelum disimpan
                </p>

            </div>

            {{-- WARNING --}}
            @if(session('warning'))

                <div style="
                                background:#fff3cd;
                                color:#856404;
                                padding:14px 18px;
                                border-radius:8px;
                                margin-bottom:20px;
                                text-align:left;
                            ">

                    <b>Beberapa data tidak disimpan:</b>

                    <ul style="margin-top:10px; margin-bottom:0;">

                        @foreach(session('warning') as $w)
                            <li>{{ $w }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            {{-- ERROR --}}
            @if(session('error'))

                <div style="
                                background:#f8d7da;
                                color:#721c24;
                                padding:14px 18px;
                                border-radius:8px;
                                margin-bottom:20px;
                            ">

                    {{ session('error') }}

                </div>

            @endif

            {{-- VALIDATION --}}
            @if ($errors->any())

                <div style="
                                background:#f8d7da;
                                color:#721c24;
                                padding:14px 18px;
                                border-radius:8px;
                                margin-bottom:20px;
                            ">

                    {{ $errors->first() }}

                </div>

            @endif

            {{-- SUCCESS --}}
            @if(session('success'))

                <div style="
                                background:#d4edda;
                                color:#155724;
                                padding:14px 18px;
                                border-radius:8px;
                                margin-bottom:20px;
                            ">

                    {{ session('success') }}

                </div>

            @endif

            {{-- FORM --}}
            <form action="{{ url('/import/preview?periode_id=' . session('periode_id')) }}" method="POST"
                enctype="multipart/form-data">

                @csrf

                {{-- FILE INPUT --}}
                <div style="margin-bottom:30px;">

                    <label style="
                                font-weight:600;
                                display:block;
                                margin-bottom:12px;
                                font-size:16px;
                            ">

                        Upload File CSV

                    </label>

                    <input type="file" name="file" required style="
                                    padding:12px;
                                    border:1px solid #dcdcdc;
                                    border-radius:8px;
                                    width:100%;
                                    font-size:15px;
                                    background:#fafafa;
                                ">

                </div>

                {{-- BUTTON TEMPLATE --}}
                <div style="
                            text-align:center;
                            margin-bottom:18px;
                        ">

                    <a href="{{ url('/download-template') }}" style="
                                    display:inline-block;
                                    background:#28a745;
                                    color:white;
                                    padding:12px 26px;
                                    border-radius:8px;
                                    text-decoration:none;
                                    font-size:15px;
                                    font-weight:500;
                                ">

                        Download Template CSV

                    </a>

                </div>

                {{-- BUTTON PREVIEW --}}
                <div style="text-align:center;">

                    <button type="submit" style="
                                    background:#007bff;
                                    color:white;
                                    border:none;
                                    padding:12px 35px;
                                    border-radius:8px;
                                    cursor:pointer;
                                    font-size:15px;
                                    font-weight:500;
                                ">

                        Preview

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection