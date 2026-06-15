@extends('layouts.app')

@section('content')

    <style>
        .hasil-card {
            max-width: 1400px;
            margin: auto;
        }

        .hasil-wrapper {
            overflow-x: auto;
        }

        #table-main {
            width: 100% !important;
        }

        body {
            overflow-x: hidden;
        }

        .text-start {
            text-align: left !important;
        }
    </style>

    <div class="card hasil-card">

        {{-- HEADER WITH TITLE AND FILTERS/EXPORTS --}}
        <div
            style="display:flex; gap:20px; align-items:center; justify-content:space-between; flex-wrap:wrap; margin-bottom:20px;">

            {{-- LEFT: Title --}}
            <h2 style="margin:0;">Hasil Pembagian Kelompok</h2>

            {{-- RIGHT: Filters + Exports --}}
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

                {{-- Filter Section --}}
                <form method="GET" action="" style="display:flex; gap:10px; align-items:center;">

                    <input type="hidden" name="periode_id" value="{{ $periode_id }}">

                    {{-- FILTER DPL --}}
                    <select name="dpl_id" class="form-control" style="width:150px;">
                        <option value="">Semua DPL</option>

                        @foreach($dplList as $dpl)
                            <option value="{{ $dpl->id_dpl }}" {{ request('dpl_id') == $dpl->id_dpl ? 'selected' : '' }}>
                                {{ $dpl->nama }}
                            </option>
                        @endforeach
                    </select>

                    {{-- FILTER APL --}}
                    <select name="apl_id" class="form-control" style="width:150px;">
                        <option value="">Semua APL</option>

                        @foreach($aplList as $apl)
                            <option value="{{ $apl->id_apl }}" {{ request('apl_id') == $apl->id_apl ? 'selected' : '' }}>
                                {{ $apl->nama }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">
                        Filter
                    </button>

                </form>

                {{-- Export Section --}}
                @php
                    $periode_id = request('periode_id') ?? session('periode_id');
                @endphp

                @if($periode_id)
                            <a href="{{ route('export.excel', [
                        'periode_id' => $periode_id,
                        'dpl_id' => request('dpl_id'),
                        'apl_id' => request('apl_id')
                    ]) }}" class="btn btn-success">
                                Export Excel
                            </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        Export Excel
                    </button>
                @endif

                @if($periode_id)
                            <a href="{{ route('export.pdf', [
                        'periode_id' => $periode_id,
                        'dpl_id' => request('dpl_id'),
                        'apl_id' => request('apl_id')
                    ]) }}" class="btn btn-danger">
                                Export PDF
                            </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        Export PDF
                    </button>
                @endif

            </div>

        </div>

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

        <form method="GET" class="mb-3">

            <input type="hidden" name="periode_id" value="{{ $periode_id }}">

            <div class="row g-2">

                {{-- Search --}}
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari kelompok, nama, NIM, prodi, DPL, APL, kecamatan, desa, atau dusun..."
                        value="{{ request('search') }}">
                </div>

                {{-- Filter Kelompok --}}
                <div class="col-md-3">
                    <select name="kelompok_id" class="form-control">

                        <option value="">
                            Semua Kelompok
                        </option>

                        @foreach($pilihanKelompok as $item)

                            <option value="{{ $item['id_kelompok'] }}" {{ request('kelompok_id') == $item['id_kelompok'] ? 'selected' : '' }}>

                                Kelompok K{{ $item['nomor_kelompok'] }}

                            </option>

                        @endforeach

                    </select>
                </div>

                {{-- Tombol Cari --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        Cari
                    </button>
                </div>

            </div>

        </form>

        @php
            $nomorKelompok = 1;
        @endphp

        @foreach($kelompok as $idKelompok => $items)

            @php
                $ketua = $items->first();
                $group = optional($ketua->kelompok);
            @endphp

            @php
                $searchData =
                    'K' . $group->nomor_kelompok . ' ' .
                    optional($group->dpl)->nama . ' ' .
                    optional($group->apl)->nama . ' ' .
                    ($group->nama_kecamatan ?? '') . ' ' .
                    ($group->desa ?? '') . ' ' .
                    ($group->dusun ?? '');
            @endphp

            <div class="card mb-4 kelompok-card searchable-card" data-search="{{ strtolower($searchData) }}">

                <div class="card-body pb-2">

                    <div class="border rounded p-3 mb-3" style="background:#f8f9fa;">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-2">
                                    <strong>Kelompok :</strong>
                                    <span class="badge bg-primary">
                                        K{{ $group->nomor_kelompok }}
                                    </span>
                                </div>

                                <div class="mb-2">
                                    <strong>DPL :</strong>
                                    {{ optional($group->dpl)->nama ?? '-' }}
                                </div>

                                <div>
                                    <strong>APL :</strong>
                                    {{ optional($group->apl)->nama ?? '-' }}
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="mb-2">
                                    <strong>Kecamatan :</strong>
                                    {{ $group->nama_kecamatan ?? '-' }}
                                </div>

                                <div class="mb-2">
                                    <strong>Desa :</strong>
                                    {{ $group->desa ?? '-' }}
                                </div>

                                <div>
                                    <strong>Dusun :</strong>
                                    {{ $group->dusun ?? '-' }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card-body pt-0">

                    <div class="card-body">

                        <table class="table table-bordered table-striped">

                            <thead>

                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Gender</th>

                                    @if($status == 0)
                                        <th>Aksi</th>
                                    @endif
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($items as $i => $p)

                                    <tr class="peserta-row">

                                        <td>{{ $i + 1 }}</td>

                                        <td class="searchable">
                                            {{ $p->nim }}
                                        </td>

                                        <td class="searchable text-start">
                                            {{ $p->nama }}
                                        </td>

                                        <td class="searchable text-start">
                                            {{ optional($p->prodiRel)->nama_prodi ?? '-' }}
                                        </td>

                                        <td class="searchable text-start">
                                            {{ in_array($p->gender, ['L', 'Pria']) ? 'Laki-Laki' : 'Perempuan' }}
                                        </td>

                                        @if($status == 0)

                                            <td>

                                                <form action="{{ route('peserta.hapus') }}" method="POST"
                                                    onsubmit="return confirm('Yakin hapus peserta?')">
                                                    @csrf

                                                    <input type="hidden" name="nim" value="{{ $p->nim }}">

                                                    <input type="hidden" name="periode_id" value="{{ $periode_id }}">

                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        Hapus
                                                    </button>

                                                </form>

                                            </td>

                                        @endif

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

        @endforeach

            <div class="d-flex justify-content-center mt-4">
                {{ $kelompok->links() }}
            </div>

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
                            <td class="text-start">
                                {{ $p->nama }}
                            </td>
                            <td class="text-start">{{ optional($p->prodiRel)->nama_prodi ?? '-' }}</td>
                            <td class="text-start">{{ $p->gender }}</td>
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
                                                ->count() }}">
                                                            K{{ $k->nomor_kelompok }}
                                                        </option>
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

        {{-- ROW 2: Action Buttons & Status --}}
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

            @if($status == 0)
                <form action="{{ route('reset.pembagian') }}" method="POST">
                    @csrf
                    <button class="btn btn-secondary"
                        onclick="return confirm('Apakah anda yakin ingin mereset semua pembagian kelompok ini?')">
                        Reset Pembagian
                    </button>
                </form>
            @endif

            {{-- Right Section: Pindah/Tukar + Publish + Status --}}
            <div style="display:flex; gap:10px; align-items:center; margin-left:auto;">

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

                @if($status == 0)
                    <form action="{{ route('kelompok.publish') }}" method="POST" style="margin:0;">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ request('periode_id') ?? session('periode_id') }}">

                        <button class="btn btn-dark"
                            onclick="return confirm('Apakah anda yakin ingin publish? Data tidak dapat diubah setelah ini!')">
                            Publish
                        </button>
                    </form>
                @elseif($status == 1)
                    <form action="{{ route('kelompok.unpublish') }}" method="POST" style="margin:0;">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ request('periode_id') ?? session('periode_id') }}">

                        <button class="btn" style="background-color: #ff8c00; border-color: #ff8c00; color: white;"
                            onclick="return confirm('Apakah anda yakin ingin unpublish? Data akan dapat diubah kembali.')">
                            Unpublish
                        </button>
                    </form>
                @endif

                {{-- STATUS DISPLAY --}}
                <div style="white-space:nowrap;">
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