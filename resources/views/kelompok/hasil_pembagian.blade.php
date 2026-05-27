@extends('layouts.app')

@section('content')

    <div class="card">

        <h2 style="margin-bottom:20px;">Hasil Pembagian Kelompok</h2>

        <form method="GET" style="margin-bottom:15px;">

            <label>Pilih Nomor KKN:</label>

            <select name="periode_id" onchange="this.form.submit()" class="form-control" style="width:250px;">

                @foreach($periodes as $periode)

                    <option value="{{ $periode->id_periode }}" {{ $periode_id == $periode->id_periode ? 'selected' : '' }}>

                        {{ $periode->nama_kkn }}

                    </option>

                @endforeach

            </select>

        </form>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div style="overflow-x:auto;">
            <table id="table-main" class="display">
                <thead style="background: #343a40; color: white;">
                    <tr>
                        <th style="text-align: center; padding: 12px;">No</th>
                        <th style="text-align: center; padding: 12px;">Kelompok</th>
                        <th style="text-align: center; padding: 12px;">NIM</th>
                        <th style="text-align: center; padding: 12px;">Nama</th>
                        <th style="text-align: center; padding: 12px;">Prodi</th>
                        <th style="text-align: center; padding: 12px;">Gender</th>
                        <th style="text-align: center; padding: 12px;">DPL</th>
                        <th style="text-align: center; padding: 12px;">Kontak DPL</th>
                        <th style="text-align: center; padding: 12px;">APL</th>
                        <th style="text-align: center; padding: 12px;">Kontak APL</th>
                        <th style="text-align: center; padding: 12px;">Kecamatan</th>
                        <th style="text-align: center; padding: 12px;">Desa</th>
                        <th style="text-align: center; padding: 12px;">Dusun</th>
                    </tr>
                </thead>

                <tbody>
                    @php $no = 1; @endphp

                    @foreach($kelompok as $id => $items)

                        @foreach($items as $p)

                            <tr style="border-bottom: 1px solid #eee;">

                                <td style="text-align: center; padding: 12px;">
                                    {{ $no++ }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    K{{ optional($p->kelompok)->nomor_kelompok ?? '-' }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ $p->nim }}
                                </td>

                                <td style="text-align: left; padding: 12px;">
                                    {{ $p->nama }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ $p->prodi }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ in_array($p->gender, ['L', 'Pria']) ? 'Laki-Laki' : 'Perempuan' }}
                                </td>

                                <td style="text-align: left; padding: 12px;">
                                    {{ optional($p->kelompok?->dpl)->nama ?? '-' }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ optional($p->kelompok?->dpl)->no_telp ?? '-' }}
                                </td>

                                <td style="text-align: left; padding: 12px;">
                                    {{ optional($p->kelompok?->apl)->nama ?? '-' }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ optional($p->kelompok?->apl)->no_telp ?? '-' }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ optional($p->kelompok)->nama_kecamatan ?? '-' }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ optional($p->kelompok)->desa ?? '-' }}
                                </td>

                                <td style="text-align: center; padding: 12px;">
                                    {{ optional($p->kelompok)->dusun ?? '-' }}
                                </td>

                            </tr>

                        @endforeach

                    @endforeach

                </tbody>
            </table>
        </div>

        <br>

        <h5>Peserta yang Belum Mendapat Kelompok</h5>

        @if($belum->count() > 0 && $kelompok->count() > 0)

            <table id="table-belum" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Gender</th>
                        <th>Bahasa Jawa</th>
                        <th>Penyakit</th>
                        <th>Khusus</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($belum as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->nim }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->prodi }}</td>
                            <td>{{ $p->gender }}</td>
                            <td>{{ $p->bahasa_jawa == 1 ? 'Bisa' : 'Tidak' }}</td>
                            <td>{{ $p->riwayat_penyakit == 1 ? 'Ya' : 'Tidak' }}</td>
                            <td>{{ $p->berkebutuhan_khusus == 1 ? 'Ya' : 'Tidak' }}</td>
                            <td>
                                @if($status == 0)
                                    <form action="{{ route('peserta.tempatkan') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="nim" value="{{ $p->nim }}">

                                        <select name="id_kelompok" required class="form-control kelompok-select">
                                            <option value="">Pilih Kelompok</option>
                                            @foreach($kelompokList as $k)
                                                        <option value="{{ $k->id_kelompok }}" data-kapasitas="{{ $k->kapasitas }}" data-isi="{{ \App\Models\Peserta::where('id_kelompok', $k->id_kelompok)
                                                ->where('id_periode', $periode_id)
                                                ->count() }}" K{{ $k->nomor_kelompok }} </option>
                                            @endforeach
                                        </select>

                                        <button class="btn btn-sm btn-success mt-1">
                                            Tempatkan
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else

            <div class="alert alert-success text-center">
                Semua peserta sudah terdistribusi ke kelompok!
            </div>

        @endif

        <br>

        <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">

            @if($status == 0)
                <form action="{{ route('reset.pembagian') }}" method="POST">
                    @csrf
                    <button class="btn btn-secondary"
                        onclick="return confirm('Apakah anda yakin ingin mereset semua pembagian kelompok ini?')">
                        Reset Pembagian
                    </button>
                </form>
            @endif

            <div style="display:flex; gap:10px;">
                @php
                    $periode_id = request('periode_id') ?? session('periode_id');
                @endphp

                @if($status == 0)
                    @if($periode_id)
                        <a href="{{ route('halaman.pindah', ['periode_id' => $periode_id]) }}" class="btn btn-warning">
                            Pindah Peserta
                        </a>
                    @else
                        <button class="btn btn-secondary" disabled>
                            Pindah Peserta
                        </button>
                    @endif
                @endif

                @if($status == 0)
                    @if($periode_id)
                        <a href="{{ route('halaman.tukar', ['periode_id' => $periode_id]) }}" class="btn btn-warning">
                            Tukar Peserta
                        </a>
                    @else
                        <button class="btn btn-secondary" disabled>
                            Tukar Peserta
                        </button>
                    @endif
                @endif

                @if($periode_id)
                    <a href="{{ route('export.excel', ['periode_id' => $periode_id]) }}" class="btn btn-success">
                        Export Excel
                    </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        Export Excel
                    </button>
                @endif

                @if($periode_id)
                    <a href="{{ route('export.pdf', ['periode_id' => $periode_id]) }}" class="btn btn-danger">
                        Export PDF
                    </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        Export PDF
                    </button>
                @endif

                @if($status == 0)
                    <form action="{{ route('kelompok.publish') }}" method="POST">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ request('periode_id') ?? session('periode_id') }}">

                        <button class="btn btn-dark"
                            onclick="return confirm('Apakah anda yakin ingin publish? Data tidak dapat diubah setelah ini!')">
                            Publish
                        </button>
                    </form>
                @elseif($status == 1)
                    <form action="{{ route('kelompok.unpublish') }}" method="POST">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ request('periode_id') ?? session('periode_id') }}">

                        <button class="btn" style="background-color: #ff8c00; border-color: #ff8c00; color: white;"
                            onclick="return confirm('Apakah anda yakin ingin unpublish? Data akan dapat diubah kembali.')">
                            Unpublish
                        </button>
                    </form>
                @endif
            </div>
            <div style="margin-top:10px;">
                <strong>Status:</strong>

                @if($status == 1)
                    <span style="color:green; font-weight:bold;">✔ Sudah Publish</span>
                @else
                    <span style="color:orange; font-weight:bold;">⏳ Belum Publish</span>
                @endif
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function () {
            $('#table-main').DataTable({
                scrollX: true
            });

            $('#table-belum').DataTable();
        });
    </script>

    <script>
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 3000);
    </script>

    <script>
        document.querySelectorAll('.kelompok-select').forEach(select => {
            select.addEventListener('change', function () {
                let kapasitas = this.options[this.selectedIndex].getAttribute('data-kapasitas');
                let isi = this.options[this.selectedIndex].getAttribute('data-isi');

                if (kapasitas && isi && parseInt(isi) >= parseInt(kapasitas)) {
                    alert('⚠️ Kelompok sudah penuh!');
                }
            });
        });
    </script>

@endsection