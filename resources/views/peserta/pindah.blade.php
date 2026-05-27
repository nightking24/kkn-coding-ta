@extends('layouts.app')

@section('content')

    <div class="card">
        <h2>Pindah Peserta</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php
            $periode_id = request('periode_id') ?? session('periode_id');
        @endphp

        <form method="POST" action="{{ route('peserta.pindah', ['periode_id' => $periode_id]) }}">
            @csrf

            <div class="mb-3">
                <label>Pilih Peserta</label>
                <select name="nim" class="form-control select2">

                    <option value="">-- Pilih Peserta --</option>

                    @foreach($peserta as $p)

                        <option value="{{ $p->nim }}">
                            {{ $p->nama }} - {{ $p->nim }} - K{{ optional($p->kelompok)->nomor_kelompok ?? '-' }}
                        </option>

                    @endforeach

                </select>
            </div>

            <div class="mb-3">
                <label>Pindah ke Kelompok</label>
                <select name="id_kelompok" class="form-control select2">
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($kelompok as $k)
                        <option value="{{ $k->id_kelompok }}">
                            K{{ $k->nomor_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-warning">Pindahkan</button>
            <a href="{{ url('/hasil-pembagian?periode_id=' . $periode_id) }}>" class="btn btn-secondary">
                Kembali
            </a>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: '-- Pilih --',
                allowClear: true
            });
        });
    </script>

@endsection