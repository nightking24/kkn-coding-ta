@extends('layouts.app')

@section('content')

    <div style="margin-bottom: 15px;">
        <a href="/dpl-view" class="btn btn-secondary mb-3">
            ← Kembali
        </a>
    </div>

    <div class="card">
        <div
            style="background: linear-gradient(135deg, #1e7e34 0%, #0f5f37 100%); color: white; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 15px 0; font-size: 28px;">Kelompok {{ $kelompok->nomor_kelompok }}</h2>
            <p style="margin: 0; font-size: 14px; opacity: 0.9;">Lokasi: Kulon Progo</p>
        </div>

        <!-- INFO KELOMPOK -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- Lokasi Section -->
            <div style="border-left: 4px solid #1e7e34; padding-left: 20px;">
                <h5 style="color: #1e7e34; margin-bottom: 15px; font-weight: 600;">📍 Lokasi</h5>
                <p style="margin: 8px 0;"><b>Kecamatan:</b> <span style="color: #666;">{{ $kelompok->nama_kecamatan }}</span></p>
                <p style="margin: 8px 0;"><b>Desa:</b> <span style="color: #666;">{{ $kelompok->desa }}</span></p>
                <p style="margin: 8px 0;"><b>Dusun:</b> <span style="color: #666;">{{ $kelompok->dusun }}</span></p>
            </div>

            <!-- APL Section -->
            <div style="border-left: 4px solid #1e7e34; padding-left: 20px;">
                <h5 style="color: #1e7e34; margin-bottom: 15px; font-weight: 600;">👨‍🏫 Pendamping</h5>
                <p style="margin: 8px 0;"><b>APL:</b> <span style="color: #666;">{{ optional($kelompok->apl)->nama }}</span>
                </p>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <div class="card">
            <h3 style="margin-bottom: 25px; color: #1e7e34; border-bottom: 3px solid #1e7e34; padding-bottom: 15px;">
                👥 Anggota Kelompok
            </h3>

            <div style="overflow-x: auto;">
                <table id="table-peserta" class="table table-hover" style="margin-bottom: 0;">
                    <thead style="background: #343a40; color: white;">
                        <tr>
                            <th style="text-align: center; padding: 12px;">No</th>
                            <th style="text-align: center; padding: 12px;">NIM</th>
                            <th style="padding: 12px;">Nama</th>
                            <th style="padding: 12px;">Prodi</th>
                            <th style="text-align: center; padding: 12px;">Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelompok->peserta as $i => $p)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="text-align: center; padding: 12px;">{{ $i + 1 }}</td>
                                <td style="text-align: center; padding: 12px; font-weight: 500;">{{ $p->nim }}</td>
                                <td style="padding: 12px;">{{ $p->nama }}</td>
                                <td style="padding: 12px;">{{ $p->prodi }}</td>
                                <td style="text-align: center; padding: 12px;">
                                    {{ $p->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#table-peserta').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25],
            });
        });
    </script>
@endsection