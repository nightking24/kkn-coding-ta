@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 style="margin-bottom: 20px;">Buat Kelompok</h2>

        {{-- Error Alert --}}
        @if(session('error'))
            <div
                style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 5px solid #dc3545;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validation Errors Alert --}}
        @if($errors->any())
            <div
                style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 5px solid #dc3545;">
                <b>Terjadi kesalahan:</b>
                <ul style="margin: 5px 0 0 15px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Success Alert --}}
        @if(session('success'))
            <div
                style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid #28a745;">
                {{ session('success') }}
            </div>
        @endif

        <form action="/kelompok/store" method="POST">
            @csrf

            <div class="form-grid">
                {{-- Nomor Kelompok --}}
                <div class="form-group">
                    <label for="nomor_kelompok">Nomor Kelompok</label>
                    <input type="text" id="nomor_kelompok" name="nomor_kelompok" class="form-control"
                        value="{{ $nextKelompok }}" readonly style="background:#e9ecef; cursor:not-allowed;">
                </div>

                {{-- Kecamatan --}}
                <div class="form-group">
                    <label for="nama_kecamatan">Kecamatan</label>
                    <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-control"
                        value="{{ old('nama_kecamatan', session('retain_kelompok.nama_kecamatan')) }}">
                </div>

                {{-- Desa --}}
                <div class="form-group">
                    <label for="desa">Desa</label>
                    <input type="text" id="desa" name="desa" class="form-control"
                        value="{{ old('desa', session('retain_kelompok.desa')) }}">
                </div>

                {{-- Dusun --}}
                <div class="form-group">
                    <label for="dusun">Dusun</label>

                    <input type="text" id="dusun" name="dusun" class="form-control">

                    <small style="color: gray; display: block; margin-top: 5px;">
                        *Autofill lokasi (kecamatan, desa, nama dukuh, kapasitas, semester, tahun kkn, faskes)
                    </small>
                </div>

                {{-- Nama Dukuh --}}
                <div class="form-group">
                    <label for="nama_dukuh">Nama Dukuh</label>
                    <input type="text" id="nama_dukuh" name="nama_dukuh" class="form-control">
                </div>

                {{-- Nama Tuan Rumah --}}
                <div class="form-group">
                    <label>Tuan Rumah</label>

                    {{-- INPUT HIDDEN UNTUK ID --}}
                    <input type="hidden" name="id_tuan_rumah" id="id_tuan_rumah">

                    {{-- INPUT NAMA --}}
                    <input list="list_tuan" id="tuan_rumah" name="tuan_rumah" class="form-control"
                        placeholder="Ketik nama tuan rumah...">

                    <datalist id="list_tuan">
                        @foreach($tuan_rumah as $t)
                            <option value="{{ $t->nama_tuan_rumah }}" data-id="{{ $t->id_tuan_rumah }}">
                        @endforeach
                    </datalist>

                    <small style="color: gray; display: block; margin-top: 5px;">
                        *Autofill data tuan rumah (nomor telepon, alamat, latitude, longitude)
                    </small>
                </div>

                {{-- Nomor Telepon --}}
                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon (Dukuh/Tuan Rumah)</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" inputmode="numeric"
                        maxlength="15">
                </div>

                {{-- Alamat --}}
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control" maxlength="255">
                </div>

                {{-- Faskes --}}
                <div class="form-group">
                    <label for="faskes">Faskes</label>
                    <select id="faskes" name="faskes" class="form-control">

                        <option value="1" {{ old('faskes', session('retain_kelompok.faskes')) == '1' ? 'selected' : '' }}>
                            Ya
                        </option>

                        <option value="0" {{ old('faskes', session('retain_kelompok.faskes')) == '0' ? 'selected' : '' }}>
                            Tidak
                        </option>

                    </select>
                </div>

                {{-- Kapasitas --}}
                <div class="form-group">
                    <label for="kapasitas">Kapasitas (Maksimal)</label>
                    <input type="number" id="kapasitas" name="kapasitas" class="form-control"
                        value="{{ old('kapasitas', session('retain_kelompok.kapasitas')) }}">
                </div>

                {{-- Semester --}}
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" class="form-control">

                        <option value="">-- Pilih Semester --</option>

                        <option value="Gasal" {{ old('semester', session('retain_kelompok.semester')) == 'Gasal' ? 'selected' : '' }}>
                            Gasal
                        </option>

                        <option value="Genap" {{ old('semester', session('retain_kelompok.semester')) == 'Genap' ? 'selected' : '' }}>
                            Genap
                        </option>

                    </select>
                </div>

                {{-- Tahun KKN --}}
                <div class="form-group">
                    <label for="tahun_kkn">Tahun KKN</label>
                    <input type="number" id="tahun_kkn" name="tahun_kkn" class="form-control"
                        value="{{ old('tahun_kkn', session('retain_kelompok.tahun_kkn')) }}" maxlength="4">
                </div>

                {{-- Latitude --}}
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="number" id="latitude" name="latitude" class="form-control" step="any" min="-90" max="90">
                    <small style="color: gray; display: block; margin-top: 5px;">
                        Contoh latitude: -7.7956
                    </small>
                </div>

                {{-- Longitude --}}
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="number" id="longitude" name="longitude" class="form-control" step="any" min="-180"
                        max="180">
                    <small style="color: gray; display: block; margin-top: 5px;">
                        Contoh longitude: 110.3695
                    </small>
                </div>

                {{-- DPL --}}
                <div class="form-group">
                    <label for="nik">DPL</label>
                    <select name="nik" id="nik" class="form-control">
                        <option value="">-- Pilih DPL --</option>
                        @foreach($dpl as $d)
                            <option value="{{ $d->nik }}">{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- APL --}}
                <div class="form-group">
                    <label for="nim">APL</label>
                    <select name="nim" id="nim" class="form-control">
                        <option value="">-- Pilih APL --</option>
                        @foreach($apl as $a)
                            <option value="{{ $a->nim }}">{{ $a->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <a href="/kelompok" class="btn btn-gray">← Kembali</a>
                <button type="submit" class="btn btn-green">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // seluruh kode jQuery dijalankan
        // setelah halaman selesai dimuat
        $(document).ready(function () {

            // =========================
            // HANDLE DATALIST TUAN RUMAH
            // =========================
            // dijalankan saat user mengetik
            // pada textbox tuan rumah
            $('#tuan_rumah').on('input', function () {

                // mengambil nilai yang sedang
                // diketik oleh pengguna
                let input = $(this).val().trim();

                // mengosongkan id_tuan_rumah
                // sebelum dilakukan pencarian ulang
                $('#id_tuan_rumah').val('');

                // memeriksa seluruh data
                // tuan rumah pada datalist
                $('#list_tuan option').each(function () {

                    if ($(this).val() === input) {

                        // menyimpan id_tuan_rumah
                        // yang sesuai dengan nama yang dipilih
                        $('#id_tuan_rumah').val(
                            $(this).data('id')
                        );

                    }

                });

            });

            // =========================
            // AUTO FILL TUAN RUMAH
            // =========================
            // dijalankan ketika pengguna
            // selesai mengisi nama tuan rumah
            $('#tuan_rumah').on('blur', function () {

                let nama = $(this).val().trim();

                if (!nama) return;

                // mengambil data tuan rumah
                // dari database tanpa reload halaman
                $.get('/get-tuan-rumah/' + encodeURIComponent(nama), function (data) {

                    // kalau data tidak ditemukan
                    if (!data) return;

                    // =========================
                    // DATA TUAN RUMAH
                    // =========================
                    // mengisi nomor telepon secara otomatis
                    // berdasarkan data tuan rumah
                    $('input[name="nomor_telepon"]').val(
                        data.nomor_telepon ?? $('input[name="nomor_telepon"]').val()
                    );

                    // mengisi alamat secara otomatis
                    $('input[name="alamat"]').val(
                        data.alamat ?? $('input[name="alamat"]').val()
                    );

                    // mengisi koordinat latitude
                    // secara otomatis
                    $('input[name="latitude"]').val(
                        data.latitude ?? $('input[name="latitude"]').val()
                    );

                    // mengisi koordinat longitude
                    // secara otomatis
                    $('input[name="longitude"]').val(
                        data.longitude ?? $('input[name="longitude"]').val()
                    );

                    // =========================
                    // DATA LOKASI
                    // =========================
                    // mengisi desa berdasarkan
                    // data tuan rumah
                    $('#desa').val(
                        data.desa ?? $('#desa').val()
                    );

                    // mengisi kecamatan secara otomatis
                    $('#nama_kecamatan').val(
                        data.nama_kecamatan ?? $('#nama_kecamatan').val()
                    );

                    // mengisi dusun secara otomatis
                    $('#dusun').val(
                        data.dusun ?? $('#dusun').val()
                    );

                    // =========================
                    // DATA KELOMPOK
                    // =========================
                    $('input[name="kapasitas"]').val(
                        data.kapasitas ?? $('input[name="kapasitas"]').val()
                    );

                    $('#semester').val(
                        data.semester ?? $('#semester').val()
                    );

                    $('input[name="tahun_kkn"]').val(
                        data.tahun_kkn ?? $('input[name="tahun_kkn"]').val()
                    );

                    $('#faskes').val(
                        data.faskes == 1 ? "1" : "0"
                    );

                    // =========================
                    // AUTO LOAD DPL APL
                    // =========================
                    if (data.desa) {
                        loadDplApl(data.desa);
                    }

                });

            });

            // =========================
            // AUTO FILL DUSUN
            // =========================
            // mengambil data lokasi
            // berdasarkan nama dusun
            $('#dusun').on('blur', function () {

                let dusun = $(this).val().trim();

                if (!dusun) return;

                // mengambil data dusun
                // dari database tanpa reload halaman
                $.get('/get-dusun/' + encodeURIComponent(dusun), function (data) {

                    // kalau data tidak ditemukan
                    if (!data) return;

                    // isi field TANPA menghapus manual input
                    $('#desa').val(data.desa ?? $('#desa').val());

                    $('#nama_kecamatan').val(
                        data.nama_kecamatan ?? $('#nama_kecamatan').val()
                    );

                    $('input[name="nama_dukuh"]').val(
                        data.nama_dukuh ?? $('input[name="nama_dukuh"]').val()
                    );

                    $('input[name="kapasitas"]').val(
                        data.kapasitas ?? $('input[name="kapasitas"]').val()
                    );

                    $('#semester').val(
                        data.semester ?? $('#semester').val()
                    );

                    $('input[name="tahun_kkn"]').val(
                        data.tahun_kkn ?? $('input[name="tahun_kkn"]').val()
                    );

                    $('#faskes').val(
                        data.faskes == 1 ? "1" : "0"
                    );

                    // load otomatis DPL APL
                    if (data.desa) {
                        loadDplApl(data.desa);
                    }

                });

            });

            // =========================
            // DESA MANUAL
            // =========================
            // dijalankan ketika
            // desa berubah
            $('#desa').on('change', function () {
                let desa = $(this).val();

                if (desa) {
                    loadDplApl(desa.trim());
                }
            });

            // mengambil daftar DPL dan APL
            // berdasarkan desa yang dipilih
            function loadDplApl(desa) {
                // mengambil data DPL dan APL
                // dari server Laravel
                $.get('/get-dpl-apl-by-desa/' + encodeURIComponent(desa), function (res) {

                    let dpl = $('#nik');
                    let apl = $('#nim');

                    // menghapus isi dropdown DPL
                    // sebelum data baru dimuat
                    dpl.html('<option value="">-- Pilih DPL --</option>');
                    apl.html('<option value="">-- Pilih APL --</option>');

                    res.dpl.forEach(d => {
                        // menambahkan daftar DPL
                        // ke dalam dropdown
                        dpl.append(`<option value="${d.nik}">${d.nama}</option>`);
                    });

                    res.apl.forEach(a => {
                        // menambahkan daftar APL
                        // ke dalam dropdown
                        apl.append(`<option value="${a.nim}">${a.nama}</option>`);
                    });
                });
            }

            // =========================
            // VALIDASI FORM
            // =========================
            // menambahkan daftar APL
            // ke dalam dropdown
            $('form').on('submit', function () {

                // mengambil nilai
                // nomor telepon
                let telepon = $('input[name="nomor_telepon"]').val();

                // VALIDASI NOMOR TELEPON SAJA
                // karena ini frontend helper
                // validasi utama tetap di Laravel

                // memastikan nomor telepon
                // minimal 10 digit
                if (telepon && telepon.length > 0 && telepon.length < 10) {

                    // menampilkan peringatan
                    // kepada pengguna
                    alert('Nomor telepon harus 10 sampai 15 digit');

                    return false;
                }

            });
        }); </script>
@endsection