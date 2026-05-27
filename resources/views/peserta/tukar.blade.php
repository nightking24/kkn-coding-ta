@extends('layouts.app')

@section('content')

    <div class="card">
        <h2>Tukar Peserta</h2>

        {{-- ALERT --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php
            $periode_id = request('periode_id') ?? session('periode_id');
        @endphp

        <form method="POST" action="{{ route('peserta.tukar', ['periode_id' => $periode_id]) }}">
            @csrf

            {{-- ================= KELOMPOK 1 ================= --}}
            <div class="mb-3">
                <label>Kelompok 1</label>
                <select id="kelompok1" class="form-control select2">
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($kelompok as $k)
                        <option value="{{ $k->id_kelompok }}">
                            K{{ $k->nomor_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Peserta 1</label>
                <select name="nim1" id="peserta1" class="form-control select2" required>
                    <option value="">-- Pilih Peserta --</option>
                </select>
            </div>

            {{-- ================= KELOMPOK 2 ================= --}}
            <div class="mb-3">
                <label>Kelompok 2</label>
                <select id="kelompok2" class="form-control select2">
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($kelompok as $k)
                        <option value="{{ $k->id_kelompok }}">
                            K{{ $k->nomor_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Peserta 2</label>
                <select name="nim2" id="peserta2" class="form-control select2" required>
                    <option value="">-- Pilih Peserta --</option>
                </select>
            </div>

            {{-- BUTTON --}}
            <button class="btn btn-primary">Tukar</button>

            <a href="{{ url('/hasil-pembagian?periode_id=' . $periode_id) }}" class="btn btn-secondary">
                Kembali
            </a>
        </form>
    </div>

    {{-- ================= SCRIPT FILTER ================= --}}
    <script>
        let peserta = @json($peserta);

        function filterPeserta(kelompokId, targetSelect) {

            let select = document.getElementById(targetSelect);

            // reset isi dropdown
            select.innerHTML = '<option value="">-- Pilih Peserta --</option>';

            peserta.forEach(p => {

                if (String(p.id_kelompok) === String(kelompokId)) {

                    let option = document.createElement('option');

                    option.value = p.nim;

                    option.text =
                        p.nama + ' - ' +
                        p.nim + ' - K' +
                        (p.kelompok ? p.kelompok.nomor_kelompok : '-');

                    select.appendChild(option);
                }
            });

            // refresh select2
            $('#' + targetSelect).trigger('change');
        }

        // 🔥 KELOMPOK 1
        $('#kelompok1').on('change', function () {
            // reset peserta 1
            $('#peserta1').empty().append('<option value="">-- Pilih Peserta --</option>').trigger('change');

            if (this.value === document.getElementById('kelompok2').value) {
                alert('Pilih kelompok yang berbeda!');
                this.value = '';
                return;
            }

            filterPeserta(this.value, 'peserta1');
        });

        // 🔥 KELOMPOK 2
        $('#kelompok2').on('change', function () {

            // reset peserta 2
            document.getElementById('peserta2').innerHTML = '<option value="">-- Pilih Peserta --</option>';

            if (this.value === document.getElementById('kelompok1').value) {
                alert('Pilih kelompok yang berbeda!');
                this.value = '';
                return;
            }

            filterPeserta(this.value, 'peserta2');
        });

        // 🔥 CEGAH PILIH PESERTA SAMA
        document.getElementById('peserta2').addEventListener('change', function () {
            if (this.value === document.getElementById('peserta1').value) {
                alert('Tidak bisa memilih peserta yang sama!');
                this.value = '';
            }
        });

        // 🔥 VALIDASI SUBMIT + DISABLE BUTTON
        document.querySelector('form').addEventListener('submit', function (e) {
            let p1 = document.getElementById('peserta1').value;
            let p2 = document.getElementById('peserta2').value;

            if (!p1 || !p2) {
                alert('Pilih kedua peserta terlebih dahulu!');
                e.preventDefault();
                return;
            }

            this.querySelector('button[type="submit"]').disabled = true;
        });
    </script>

    <script>
        $(document).ready(function () {

            $('.select2').select2({
                width: '100%',
                placeholder: '-- Pilih Data --',
                allowClear: true
            });

        });
    </script>

@endsection