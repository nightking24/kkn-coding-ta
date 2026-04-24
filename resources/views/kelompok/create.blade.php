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
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" inputmode="numeric">
                </div>

                {{-- Kecamatan --}}
                <div class="form-group">
                    <label for="nama_kecamatan">Kecamatan</label>
                    <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-control">
                </div>

                {{-- Desa --}}
                <div class="form-group">
                    <label for="desa">Desa</label>
                    <input type="text" id="desa" name="desa" class="form-control">
                </div>

                {{-- Dusun --}}
                <div class="form-group">
                    <label for="dusun">Dusun</label>
                    <input type="text" id="dusun" name="dusun" class="form-control">
                </div>

                {{-- Nama Dukuh --}}
                <div class="form-group">
                    <label for="nama_dukuh">Nama Dukuh</label>
                    <input type="text" id="nama_dukuh" name="nama_dukuh" class="form-control">
                </div>

                {{-- Nama Tuan Rumah --}}
                <div class="form-group">
                    <label>Tuan Rumah</label>
                    <select id="tuan_rumah" name="id_tuan_rumah" class="form-control" style="width:100%"
                        required></select>
                </div>

                {{-- Nomor Telepon --}}
                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" inputmode="numeric">
                </div>

                {{-- Alamat --}}
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control">
                </div>

                {{-- Faskes --}}
                <div class="form-group">
                    <label for="faskes">Faskes</label>
                    <select id="faskes" name="faskes" class="form-control">
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>

                {{-- Kapasitas --}}
                <div class="form-group">
                    <label for="kapasitas">Kapasitas</label>
                    <input type="number" id="kapasitas" name="kapasitas" class="form-control">
                </div>

                {{-- Semester --}}
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" class="form-control">
                        <option value="">-- Pilih Semester --</option>
                        <option value="Gasal">Gasal</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>

                {{-- Tahun KKN --}}
                <div class="form-group">
                    <label for="tahun_kkn">Tahun KKN</label>
                    <input type="number" id="tahun_kkn" name="tahun_kkn" class="form-control">
                </div>

                {{-- Latitude --}}
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="number" id="latitude" name="latitude" class="form-control" step="any" required min="-90"
                        max="90">
                </div>

                {{-- Longitude --}}
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="number" id="longitude" name="longitude" class="form-control" step="any" required min="-180"
                        max="180">
                    <small style="color: gray; display: block; margin-top: 5px;">
                        Contoh koordinat: Latitude -7.7956, Longitude 110.3695
                    </small>
                </div>

                {{-- DPL --}}
                <div class="form-group">
                    <label for="nik">DPL</label>
                    <select id="nik" name="nik" class="form-control">
                        <option value="">-- Pilih DPL --</option>
                        @foreach($dpl as $d)
                            <option value="{{ $d->nik }}">{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- APL --}}
                <div class="form-group">
                    <label for="nim">APL</label>
                    <select id="nim" name="nim" class="form-control">
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
        $(document).ready(function () {

            $('#tuan_rumah').select2({
                placeholder: 'Cari / ketik nama tuan rumah...',
                allowClear: true,
                minimumInputLength: 1,

                ajax: {
                    url: '/search-tuan-rumah',
                    dataType: 'json',
                    delay: 250,

                    data: function (params) {
                        return {
                            keyword: params.term
                        };
                    },

                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id_tuan_rumah,
                                    text: item.nama_tuan_rumah,
                                    data: item
                                };
                            })
                        };
                    }
                },

                tags: true // 🔥 biar bisa input manual juga
            });

            // 🔥 AUTO FILL SAAT DIPILIH
            $('#tuan_rumah').on('select2:select', function (e) {
                let data = e.params.data.data;

                if (data) {
                    $('input[name="dusun"]').val(data.dusun);
                    $('input[name="desa"]').val(data.desa);
                    $('input[name="nomor_telepon"]').val(data.nomor_telepon);
                    $('input[name="alamat"]').val(data.alamat);
                    $('input[name="latitude"]').val(data.latitude);
                    $('input[name="longitude"]').val(data.longitude);
                }
            });

            // 🔥 VALIDASI FORM
            $('form').on('submit', function () {
                let nama = $('#tuan_rumah').val();

                if (!nama || nama.trim() === '') {
                    alert('Nama tuan rumah wajib diisi');
                    return false;
                }
            });

        });
    </script>
@endsection