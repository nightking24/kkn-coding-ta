@extends('layouts.app')

@section('content')

    <div class="card">
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 10px; color: #000;">Selamat datang, <b style="color: #1e7e34;">{{ session('user')->username ?? 'User' }}</b></h3>
            <p style="color: #666; font-size: 14px; margin: 0;">Portal Pembagian Kelompok KKN Reguler</p>
        </div>

        <h3 style="margin-bottom: 25px; color: #1e7e34; border-bottom: 3px solid #1e7e34; padding-bottom: 15px;">
            📊 Dashboard KKN Reguler
        </h3>

        <form method="GET" action="/dashboard">
            <div style="display:flex; flex-direction:column; gap:5px;">
                <label style="font-size:13px; color:#555;">Pilih Periode</label>

                <select name="periode_id" onchange="this.form.submit()" style="
            padding:8px 12px;
            border-radius:8px;
            border:1px solid #ced4da;
            min-width:220px;
        ">
        
                    @foreach(\App\Models\Periode::all() as $p)
                        <option value="{{ $p->id_periode }}" {{ session('periode_id') == $p->id_periode ? 'selected' : '' }}>
                            {{ $p->nama_kkn }} ({{ $p->tahun_kkn }})
                        </option>
                    @endforeach
                </select>
        </form>

        @if($periode)
            <h3>{{ $periode->nama_kkn }} ({{ $periode->tahun_kkn }})</h3>
            <p>Lokasi: {{ $periode->lokasi }}</p>
        @else
            <p style="color:red;">Belum ada data periode</p>
        @endif

        <p>
            Status:
            @if($periode && $periode->status_publish == 1)
                <span style="color:green;">✔ Sudah Publish</span>
            @else
                <span style="color:orange;">⏳ Belum Publish</span>
            @endif
        </p>

        <div style="display:flex; gap:10px; margin-top:15px;">

            <div style="flex:1; background:#f1f3f5; padding:10px; border-radius:6px; text-align:center;">
                <b>{{ $peserta }}</b><br>Peserta
            </div>

            <div style="flex:1; background:#f1f3f5; padding:10px; border-radius:6px; text-align:center;">
                <b>{{ $kelompok }}</b><br>Kelompok
            </div>

            <div style="flex:1; background:#f1f3f5; padding:10px; border-radius:6px; text-align:center;">
                <b>{{ $dpl }}</b><br>DPL
            </div>

            <div style="flex:1; background:#f1f3f5; padding:10px; border-radius:6px; text-align:center;">
                <b>{{ $apl }}</b><br>APL
            </div>

        </div>

        <div style="margin-top:20px;">
            <a href="{{ url('/periode/' . $periode->id_periode . '?periode_id=' . session('periode_id')) }}"
                class="btn btn-blue">
                Detail
            </a>
        </div>

    </div>

@endsection