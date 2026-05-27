@extends('layouts.app')

@section('content')

<div class="card">

    <div style="margin-bottom: 15px;">
        <h3 style="margin-bottom: 10px; color: #000;">
            Selamat datang,
            <b style="color: #1e7e34;">
                {{ $peserta->nama }}
            </b>
        </h3>

        <p style="color: #666; font-size: 14px; margin: 0;">
            Portal Pembagian Kelompok KKN Reguler
        </p>
    </div>

    <hr style="margin: 12px 0; border: none; border-top: 2px solid #eee;">

    {{-- ========================= --}}
    {{-- CEK PESERTA & KELOMPOK --}}
    {{-- ========================= --}}

    @if(isset($peserta) && $peserta && $peserta->kelompok)

        {{-- HEADER KELOMPOK --}}
        <div style="
            background: linear-gradient(135deg, #1e7e34 0%, #0f5f37 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        ">

            <h2 style="margin: 0 0 15px 0; font-size: 28px;">
                Kelompok {{ $peserta->kelompok->nomor_kelompok }}
            </h2>

            <p style="margin: 0 0 8px 0; font-size: 14px; opacity: 0.9;">
                Periode:
                {{ optional($peserta->kelompok->periode)->tahun_kkn ?? '-' }}
            </p>

            <p style="margin: 0; font-size: 14px; opacity: 0.9;">
                Lokasi:
                {{ optional($peserta->kelompok->periode)->lokasi }}
            </p>
        </div>

        {{-- DETAIL --}}
        <div style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        ">

            {{-- LOKASI --}}
            <div style="
                border-left: 4px solid #1e7e34;
                padding-left: 20px;
            ">

                <h5 style="
                    color: #1e7e34;
                    margin-bottom: 15px;
                    font-weight: 600;
                ">
                    📍 Detail Lokasi
                </h5>

                <p><b>Kecamatan:</b>
                    {{ $peserta->kelompok->nama_kecamatan }}
                </p>

                <p><b>Desa:</b>
                    {{ $peserta->kelompok->desa }}
                </p>

                <p><b>Dusun:</b>
                    {{ $peserta->kelompok->dusun }}
                </p>

            </div>

            {{-- DPL --}}
            <div style="
                border-left: 4px solid #1e7e34;
                padding-left: 20px;
            ">

                <h5 style="
                    color: #1e7e34;
                    margin-bottom: 15px;
                    font-weight: 600;
                ">
                    👨‍🏫 Dosen Pembimbing Lapangan (DPL)
                </h5>

                <p>
                    <b>DPL:</b>
                    {{ optional($peserta->kelompok->dpl)->nama }}
                </p>

                <p>
                    <b>No HP:</b>
                    {{ optional($peserta->kelompok->dpl)->no_telp }}
                </p>

            </div>
        </div>

        {{-- APL --}}
        <div style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        ">

            <div style="
                border-left: 4px solid #1e7e34;
                padding-left: 20px;
            ">

                <h5 style="
                    color: #1e7e34;
                    margin-bottom: 15px;
                    font-weight: 600;
                ">
                    👨‍💼 Asisten Pembimbing Lapangan (APL)
                </h5>

                <p>
                    <b>APL:</b>
                    {{ optional($peserta->kelompok->apl)->nama }}
                </p>

                <p>
                    <b>No HP:</b>
                    {{ optional($peserta->kelompok->apl)->no_telp }}
                </p>

            </div>
        </div>

    @else

        {{-- BELUM DAPAT KELOMPOK --}}
        <div style="
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 8px;
        ">

            <p style="
                font-size: 16px;
                color: #dc3545;
                margin: 0;
            ">
                ⚠️ Belum mendapatkan kelompok
            </p>

            <p style="
                font-size: 14px;
                color: #666;
                margin-top: 10px;
            ">
                Silakan hubungi admin untuk informasi lebih lanjut
            </p>

        </div>

    @endif

</div>

{{-- ========================= --}}
{{-- TABEL ANGGOTA --}}
{{-- ========================= --}}

@if(isset($peserta) && $peserta && $peserta->kelompok)

<div style="margin-top: 30px;">

    <div class="card">

        <h3 style="
            margin-bottom: 25px;
            color: #1e7e34;
            border-bottom: 3px solid #1e7e34;
            padding-bottom: 15px;
        ">
            👥 Anggota Kelompok
        </h3>

        <div style="overflow-x: auto;">

            <table class="table table-hover" style="margin-bottom: 0;">

                <thead style="
                    background: #343a40;
                    color: white;
                ">

                    <tr>
                        <th style="text-align:center;">No</th>
                        <th style="text-align:center;">NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th style="text-align:center;">Gender</th>
                        <th style="text-align:center;">No. Telp</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($peserta->kelompok->peserta as $i => $p)

                        <tr>

                            <td style="text-align:center;">
                                {{ $i + 1 }}
                            </td>

                            <td style="text-align:center;">
                                {{ $p->nim }}
                            </td>

                            <td>
                                {{ $p->nama }}
                            </td>

                            <td>
                                {{ $p->prodi }}
                            </td>

                            <td style="text-align:center;">

                                @if(
                                    $p->gender == 'L' ||
                                    $p->gender == 'Pria' ||
                                    $p->gender == 'Laki-Laki'
                                )

                                    Laki-Laki

                                @else

                                    Perempuan

                                @endif

                            </td>

                            <td style="text-align:center;">
                                {{ $p->no_telp ?? '-' }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endif

@endsection