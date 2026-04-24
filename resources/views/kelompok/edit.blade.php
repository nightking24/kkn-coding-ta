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

                <!-- 🔥 AUTOCOMPLETE TUAN RUMAH -->
                <div class="form-group">
                    <label>Nama Tuan Rumah</label>
                    <select id="tuan_rumah" name="id_tuan_rumah" class="form-control" style="width:100%" required>
                        @if($data->tuanRumah)
                            <option value="{{ $data->tuanRumah->id_tuan_rumah }}" selected>
                                {{ $data->tuanRumah->nama_tuan_rumah }}
                            </option>
                        @endif
                    </select>
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
                    <select name="faskes" class="form-control">
                        <option value="1" {{ $data->faskes ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ !$data->faskes ? 'selected' : '' }}>Tidak</option>
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
                    <select name="nik" class="form-control">
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
                    <select name="nim" class="form-control">
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

                tags: true
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

            // Validasi form submission
            $('form').on('submit', function () {
                let nama = $('#tuan_rumah').val().trim();

                if (!nama) {
                    alert('Nama tuan rumah wajib diisi');
                    return false;
                }
            });
        });
    </script>
@endsection