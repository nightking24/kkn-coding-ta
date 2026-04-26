@extends('layouts.app')

@section('content')

    <div class="card">

        <h2 style="margin-bottom:20px;">Edit Kelompok</h2>

        @if ($errors->any())
            <div style="background:#f8d7da; color:#721c24; padding:12px; margin-bottom:15px;">
                <b>Terjadi kesalahan:</b>
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/kelompok/update/{{ $data->id_kelompok }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>Nomor Kelompok</label>
                    <input type="text" name="nomor_kelompok" class="form-control" value="{{ $data->nomor_kelompok }}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <input type="text" name="nama_kecamatan" class="form-control" value="{{ $data->nama_kecamatan }}">
                </div>

                <div class="form-group">
                    <label>Desa</label>
                    <input type="text" name="desa" class="form-control" value="{{ $data->desa }}">
                </div>

                <div class="form-group">
                    <label>Dusun</label>
                    <input type="text" name="dusun" class="form-control" value="{{ $data->dusun }}">
                </div>

                <div class="form-group">
                    <label>Nama Dukuh</label>
                    <input type="text" name="nama_dukuh" class="form-control" value="{{ $data->nama_dukuh }}">
                </div>

                <div class="form-group">
                    <label>Tuan Rumah</label>

                    <input list="list_tuan" id="tuan_rumah" name="id_tuan_rumah" class="form-control"
                        value="{{ optional($data->tuanRumah)->nama_tuan_rumah }}" required>

                    <datalist id="list_tuan">
                        @foreach($tuan_rumah as $t)
                            <option value="{{ $t->nama_tuan_rumah }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" class="form-control" value="{{ $data->nomor_telepon }}"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ $data->alamat }}">
                </div>

                <div class="form-group">
                    <label>Faskes</label>
                    <select name="faskes" id="faskes" class="form-control">
                        <option value="1" {{ $data->faskes == 1 ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ $data->faskes == 0 ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kapasitas</label>
                    <input type="number" name="kapasitas" class="form-control" value="{{ $data->kapasitas }}">
                </div>

                <div class="form-group">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        <option value="Gasal" {{ $data->semester == 'Gasal' ? 'selected' : '' }}>Gasal</option>
                        <option value="Genap" {{ $data->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun KKN</label>
                    <input type="number" name="tahun_kkn" class="form-control" value="{{ $data->tahun_kkn }}">
                </div>

                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" step="any" name="latitude" class="form-control" value="{{ $data->latitude }}"
                        min="-90" max="90">
                </div>

                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" step="any" name="longitude" class="form-control" value="{{ $data->longitude }}"
                        min="-180" max="180">

                    <small style="color:gray;">
                        Contoh: Latitude -7.7956, Longitude 110.3695
                    </small>
                </div>

                <!-- 🔥 PINDAH KE LUAR (INI YANG ERROR KAMU TADI) -->
                <div class="form-group">
                    <label>DPL</label>
                    <select name="nik" id="nik" class="form-control">
                        <option value="">-- Pilih DPL --</option>
                        @foreach($dpl as $d)
                            <option value="{{ $d->nik }}" {{ $data->nik == $d->nik ? 'selected' : '' }}>
                                {{ $d->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>APL</label>
                    <select name="nim" id="nim" class="form-control">
                        <option value="">-- Pilih APL --</option>
                        @foreach($apl as $a)
                            <option value="{{ $a->nim }}" {{ $data->nim == $a->nim ? 'selected' : '' }}>
                                {{ $a->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div style="margin-top:25px;">
                <a href="/kelompok" class="btn btn-gray">← Kembali</a>
                <button type="submit" class="btn btn-blue">Update</button>
            </div>

        </form>

    </div>

@endsection


@section('scripts')
    <script>
        $(document).ready(function () {

            let cacheKecamatan = {};
            function getKecamatan(nama, callback) {
                if (cacheKecamatan[nama]) {
                    callback(cacheKecamatan[nama]);
                    return;
                }

                $.get('/get-kecamatan/' + nama, function (data) {
                    cacheKecamatan[nama] = data;
                    callback(data);
                });
            }

            // =========================
            // AUTO FILL TUAN RUMAH
            // =========================
            $('#tuan_rumah').on('change', function () {

                let nama = $(this).val();

                if (!nama) return;

                $.get('/get-tuan-rumah/' + nama, function (data) {

                    if (data) {
                        $('input[name="nomor_telepon"]').val(data.nomor_telepon ?? '');
                        $('input[name="alamat"]').val(data.alamat ?? '');
                        $('input[name="latitude"]').val(data.latitude ?? '');
                        $('input[name="longitude"]').val(data.longitude ?? '');
                        $('input[name="nama_dukuh"]').val(data.nama_tuan_rumah ?? '');
                        $('select[name="faskes"]').val(data.faskes);
                    }
                });

            });

            // =========================
            // DESA MANUAL
            // =========================
            $('#desa').on('change', function () {
                let desa = $(this).val();

                if (desa) {
                    loadDplApl(desa.trim());

                }
            });

            // =========================
            // AUTO FILL KECAMATAN (CACHE)
            // =========================
            $('input[name="nama_kecamatan"]').on('change', function () {
                let kecamatan = $(this).val();

                if (!kecamatan) return;

                getKecamatan(kecamatan, function (data) {

                    if (data) {
                        $('#desa').val(data.desa.trim()).trigger('change');
                        $('#dusun').val(data.dusun);
                        $('input[name="kapasitas"]').val(data.kapasitas ?? '');
                        $('#semester').val(data.semester ?? '');
                        $('input[name="tahun_kkn"]').val(data.tahun_kkn ?? '');
                    }
                });
            });

            function loadDplApl(desa) {
                $.get('/get-dpl-apl-by-desa/' + encodeURIComponent(desa), function (res) {

                    let dpl = $('#nik');
                    let apl = $('#nim');

                    dpl.html('<option value="">-- Pilih DPL --</option>');
                    apl.html('<option value="">-- Pilih APL --</option>');

                    res.dpl.forEach(d => {
                        dpl.append(`<option value="${d.nik}">${d.nama}</option>`);
                    });

                    res.apl.forEach(a => {
                        apl.append(`<option value="${a.nim}">${a.nama}</option>`);
                    });
                });
            }

            // =========================
            // VALIDASI FORM
            // =========================
            $('form').on('submit', function () {
                let nama = $('#tuan_rumah').val();
                let telepon = $('input[name="nomor_telepon"]').val();

                if (!nama || nama.trim() === '') {
                    alert('Nama tuan rumah wajib diisi');
                    return false;
                }

                if (!telepon || telepon.length < 10) {
                    alert('Nomor telepon tidak valid');
                    return false;
                }
            });

        });
    </script>
@endsection