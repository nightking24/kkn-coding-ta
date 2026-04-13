@extends('layouts.app')

@section('content')

    <div class="card">
        <h2>Tukar Peserta</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('peserta.tukar', ['periode_id' => session('periode_id')]) }}">
            @csrf

            <div class="mb-3">
                <label>Peserta 1</label>
                <select name="nim1" class="form-control" required>
                    <option value="">-- Pilih Peserta --</option>
                    @foreach($peserta as $p)
                        <option value="{{ $p->nim }}">
                            {{ $p->nim }} - {{ $p->nama }} (K{{ optional($p->kelompok)->nomor_kelompok ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Peserta 2</label>
                <select name="nim2" class="form-control" required>
                    <option value="">-- Pilih Peserta --</option>
                    @foreach($peserta as $p)
                        <option value="{{ $p->nim }}">
                            {{ $p->nim }} - {{ $p->nama }} (K{{ optional($p->kelompok)->nomor_kelompok ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary">Tukar</button>
            <a href="{{ url('/hasil-pembagian?periode_id=' . session('periode_id')) }}" class="btn btn-secondary">
                Kembali
            </a>
        </form>
    </div>

@endsection