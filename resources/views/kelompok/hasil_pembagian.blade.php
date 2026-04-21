@extends('layouts.app')

@section('content')

    <div class="card">

        <h2 style="margin-bottom:20px;">Hasil Pembagian Kelompok</h2>

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
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kelompok</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Gender</th>
                        <th>Bahasa Jawa</th>
                        <th>Penyakit</th>
                        <th>Khusus</th>
                        <th>Desa</th>
                        <th>Dusun</th>
                        <th>Dukuh</th>
                        <th>Tuan Rumah</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Faskes</th>
                        <th>Kapasitas</th>
                        <th>Semester</th>
                        <th>Tahun</th>
                        <th>Lat</th>
                        <th>Long</th>
                        <th>DPL</th>
                        <th>Nomor Telepon DPL</th>
                        <th>APL</th>
                        <th>Nomor Telepon APL</th>
                    </tr>
                </thead>

                <tbody>
                    @php $no = 1; @endphp

                    @foreach($kelompok as $id => $items)
                        @foreach($items as $p)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>K{{ optional($p->kelompok)->nomor_kelompok ?? '-' }}</td>
                                    <td>{{ $p->nim }}</td>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->prodi }}</td>
                                    <td>{{ $p->gender }}</td>
                                    <td>{{ $p->bahasa_jawa == 1 ? 'Bisa' : 'Tidak' }}</td>
                                    <td>{{ $p->riwayat_penyakit == 1 ? 'Ya' : 'Tidak' }}</td>
                                    <td>{{ $p->berkebutuhan_khusus == 1 ? 'Ya' : 'Tidak' }}</td>
                                    <td>{{ optional($p->kelompok)->desa ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->dusun ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->nama_dukuh ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->nama_tuan_rumah ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->nomor_telepon ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->alamat ?? '-' }}</td>
                                    <td>
                                        {{
                            optional($p->kelompok)->faskes === 1 ? 'Ada' :
                            (optional($p->kelompok)->faskes === 0 ? 'Tidak Ada' : '-')
                                                            }}
                                    </td>
                                    <td>{{ optional($p->kelompok)->kapasitas ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->semester ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->tahun_kkn ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->latitude ?? '-' }}</td>
                                    <td>{{ optional($p->kelompok)->longitude ?? '-' }}</td>
                                    <td>
                                        @if($status == 0)
                                            <form action="{{ route('assign.dpl') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id_kelompok" value="{{ optional($p->kelompok)->id_kelompok }}">

                                                <select name="nik" onchange="this.form.submit()">
                                                    <option value="">Pilih DPL</option>
                                                    @foreach($dplList as $dpl)
                                                        <option value="{{ $dpl->nik }}" @selected(optional($p->kelompok)->nik == $dpl->nik)>
                                                            {{ $dpl->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <span>{{ optional($p->kelompok?->dpl)->nama ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span>
                                            {{ optional($p->kelompok?->dpl)->no_telp ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($status == 0)
                                            <form action="{{ route('assign.apl') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id_kelompok" value="{{ optional($p->kelompok)->id_kelompok }}">

                                                <select name="nim" onchange="this.form.submit()">
                                                    <option value="">Pilih APL</option>
                                                    @foreach($aplList as $apl)
                                                        <option value="{{ $apl->nim }}" @selected($p->kelompok?->apl?->nim == $apl->nim)>
                                                            {{ $apl->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <span>{{ optional($p->kelompok?->apl)->nama ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span>
                                            {{ optional($p->kelompok?->apl)->no_telp ?? '-' }}
                                        </span>
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
                                                <option value="{{ $k->id_kelompok }}" data-kapasitas="{{ $k->kapasitas }}"
                                                    data-isi="{{ \App\Models\Peserta::where('id_kelompok', $k->id_kelompok)->count() }}">
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

            @if($status == 0)
                <form action="{{ route('reset.total') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger" onclick="return confirm('INI AKAN MENGHAPUS SEMUA DATA!')">
                        Reset Total
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