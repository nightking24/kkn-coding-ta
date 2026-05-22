<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Kelompok;
use App\Models\Dpl;
use App\Models\Apl;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesertaExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Periode;
use App\Exports\HasilPembagianExport;
use App\Models\LogActivity;

class KelompokController extends Controller
{
    private function setPeriodeSession()
    {
        if (request('periode_id')) {
            session(['periode_id' => request('periode_id')]);
        }
    }

    private function getPeriodeId()
    {
        return session('periode_id')
            ?? request('periode_id')
            ?? Periode::where('status', 'aktif')
                ->value('id_periode');
    }

    private function checkPublishLock($periode_id)
    {
        $status = \App\Models\Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        if ($status == 1) {
            return back()->with('error', 'Periode sudah dipublish, data tidak bisa diubah!');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($request->periode_id) {
            session(['periode_id' => $request->periode_id]);
        }

        $periodes = Periode::all();

        $periode_id = $this->getPeriodeId();

        if (!$periode_id) {
            $periode_id = Periode::where('status_publish', 1)
                ->value('id_periode');
        }

        $kelompok = Kelompok::with(['tuanRumah', 'dpl', 'apl'])
            ->where('id_periode', $periode_id)->get();

        return view('kelompok.index', compact(
            'kelompok',
            'periodes',
            'periode_id'
        ));
    }

    public function create()
    {
        $periode_id = $this->getPeriodeId();

        $dpl = Dpl::where('id_periode', $periode_id)->get();
        $apl = Apl::where('id_periode', $periode_id)->get();

        $tuan_rumah = DB::table('tuan_rumah')->get();

        return view('kelompok.create', compact('dpl', 'apl', 'tuan_rumah'));
    }

    public function getDusun($dusun)
    {
        // ambil data kelompok berdasarkan dusun
        $data = Kelompok::where('dusun', $dusun)->first();

        if (!$data) {
            return response()->json(null);
        }

        return response()->json([

            // lokasi
            'desa' => $data->desa,
            'nama_kecamatan' => $data->nama_kecamatan,
            'dusun' => $data->dusun,

            // data kelompok
            'nama_dukuh' => $data->nama_dukuh,
            'faskes' => $data->faskes,
            'kapasitas' => $data->kapasitas,
            'semester' => $data->semester,
            'tahun_kkn' => $data->tahun_kkn,

        ]);
    }

    public function store(Request $request)
    {
        $this->logAktivitas(
            'Tambah Kelompok',
            "Menambah kelompok K{$request->nomor_kelompok}"
        );

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // ✅ VALIDASI
        $validated = $request->validate([
            'nomor_kelompok' => 'required|integer|min:1',
            'desa' => 'required',
            'dusun' => 'required',
            'nama_dukuh' => 'required',
            'id_tuan_rumah' => 'required|string|max:255',
            'nomor_telepon' => 'required|digits_between:10,15',
            'alamat' => 'required',
            'faskes' => 'required|in:0,1',
            'kapasitas' => 'required|integer|min:1',
            'semester' => 'required',
            'tahun_kkn' => 'required|digits:4',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'nama_kecamatan' => 'required',
            'nik' => 'required',
            'nim' => 'required',
        ], [
            // NOMOR KELOMPOK
            'nomor_kelompok.required' => 'Nomor Kelompok wajib diisi',
            'nomor_kelompok.integer' => 'Nomor Kelompok harus angka',
            'nomor_kelompok.min' => 'Nomor Kelompok minimal 1',

            // KECAMATAN
            'nama_kecamatan.required' => 'Nama Kecamatan wajib diisi',

            // DESA
            'desa.required' => 'Desa wajib diisi',

            // DUSUN
            'dusun.required' => 'Dusun wajib diisi',

            // NAMA DUKUH
            'nama_dukuh.required' => 'Nama Dukuh wajib diisi',

            // TUAN RUMAH
            'id_tuan_rumah.required' => 'Nama Tuan Rumah wajib diisi',

            // ALAMAT
            'alamat.required' => 'Alamat wajib diisi',

            // SEMESTER
            'semester.required' => 'Semester wajib dipilih',

            // TAHUN KKN
            'tahun_kkn.required' => 'Tahun KKN wajib diisi',
            'tahun_kkn.digits' => 'Tahun KKN harus 4 digit',

            // NOMOR TELEPON
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'nomor_telepon.digits_between' => 'Nomor Telepon harus 10 sampai 15 digit',

            // KAPASITAS
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.integer' => 'Kapasitas harus angka',
            'kapasitas.min' => 'Kapasitas minimal 1',

            // LATITUDE
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.numeric' => 'Latitude harus angka',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',

            // LONGITUDE
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.numeric' => 'Longitude harus angka',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',

            // DPL
            'nik.required' => 'DPL wajib dipilih',

            // APL
            'nim.required' => 'APL wajib dipilih',
        ]);

        // HANDLE TUAN RUMAH (FIX AMAN)

        // 🔥 Coba cek apakah input adalah ID yang valid
        $tuanById = DB::table('tuan_rumah')
            ->where('id_tuan_rumah', $request->id_tuan_rumah)
            ->first();

        if ($tuanById) {

            // ✅ ARTINYA PILIH DARI DROPDOWN (ID VALID)
            $id_tuan_rumah = $tuanById->id_tuan_rumah;

        } else {

            // ✅ ARTINYA INPUT MANUAL (NAMA)
            $nama = trim($request->id_tuan_rumah);

            // 🔥 CEK DUPLIKAT (CASE INSENSITIVE)
            $tuan = DB::table('tuan_rumah')
                ->whereRaw('LOWER(nama_tuan_rumah) = ?', [strtolower($nama)])
                ->first();

            if (!$tuan) {

                // 🔥 INSERT BARU
                $id_tuan_rumah = DB::table('tuan_rumah')->insertGetId([
                    'nama_tuan_rumah' => $nama,
                    'dusun' => $request->dusun,
                    'desa' => $request->desa,
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'nama_kecamatan' => $request->nama_kecamatan
                ]);

            } else {

                // 🔥 SUDAH ADA → PAKAI YANG LAMA
                $id_tuan_rumah = $tuan->id_tuan_rumah;
            }
        }

        // UPDATE hanya kalau DATA SUDAH ADA
        if (isset($id_tuan_rumah)) {
            DB::table('tuan_rumah')
                ->where('id_tuan_rumah', $id_tuan_rumah)
                ->update([
                    'dusun' => $request->dusun,
                    'desa' => $request->desa,
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'nama_kecamatan' => $request->nama_kecamatan
                ]);
        }
        try {

            $id_periode = $this->getPeriodeId();
            Kelompok::create([
                'nomor_kelompok' => $request->nomor_kelompok,
                'desa' => $request->desa,
                'dusun' => $request->dusun,
                'nama_dukuh' => $request->nama_dukuh,
                'id_tuan_rumah' => $id_tuan_rumah,
                'nomor_telepon' => $request->nomor_telepon,
                'alamat' => $request->alamat,
                'faskes' => $request->faskes,
                'kapasitas' => $request->kapasitas,
                'semester' => $request->semester,
                'tahun_kkn' => $request->tahun_kkn,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'nama_kecamatan' => $request->nama_kecamatan,
                'nik' => $request->nik,
                'nim' => $request->nim,
                'id_periode' => $id_periode
            ]);

            session([
                'retain_kelompok' => [
                    'nama_kecamatan' => $request->nama_kecamatan,
                    'desa' => $request->desa,
                    'faskes' => $request->faskes,
                    'kapasitas' => $request->kapasitas,
                    'semester' => $request->semester,
                    'tahun_kkn' => $request->tahun_kkn,
                ]
            ]);
            return redirect()->back()->with('success', 'Data kelompok berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data'])->withInput();
        }
    }

    public function edit($id)
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $data = Kelompok::where('id_periode', $periode_id)
            ->where('id_kelompok', $id)
            ->firstOrFail();

        $dpl = Dpl::where('id_periode', $periode_id)->get();
        $apl = Apl::where('id_periode', $periode_id)->get();
        $tuan_rumah = DB::table('tuan_rumah')->get();

        return view('kelompok.edit', compact('data', 'dpl', 'apl', 'tuan_rumah'));
    }

    public function update(Request $request, $id)
    {
        $this->logAktivitas(
            'Update Kelompok',
            "Update kelompok K{$request->nomor_kelompok}"
        );

        $periode_id = request('periode_id')
            ?? session('periode_id')
            ?? Periode::where('status_publish', 0)->value('id_periode');

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $validated = $request->validate([
            'nomor_kelompok' => 'required|integer|min:1',
            'desa' => 'required|string|max:255',
            'dusun' => 'required|string|max:255',
            'nama_dukuh' => 'required|string|max:255',
            'id_tuan_rumah' => 'required|string|max:255',
            'nomor_telepon' => 'required|digits_between:10,15',
            'alamat' => 'required|string|max:255',
            'faskes' => 'required|in:0,1',
            'kapasitas' => 'required|integer|min:1',
            'semester' => 'required|in:Gasal,Genap',
            'tahun_kkn' => 'required|digits:4',
            'nama_kecamatan' => 'required|string|max:255',
            'nik' => 'required',
            'nim' => 'required',

            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            // NOMOR KELOMPOK
            'nomor_kelompok.required' => 'Nomor Kelompok wajib diisi',
            'nomor_kelompok.integer' => 'Nomor Kelompok harus angka',
            'nomor_kelompok.min' => 'Nomor Kelompok minimal 1',

            // KECAMATAN
            'nama_kecamatan.required' => 'Nama Kecamatan wajib diisi',

            // DESA
            'desa.required' => 'Desa wajib diisi',

            // DUSUN
            'dusun.required' => 'Dusun wajib diisi',

            // NAMA DUKUH
            'nama_dukuh.required' => 'Nama Dukuh wajib diisi',

            // TUAN RUMAH
            'id_tuan_rumah.required' => 'Nama Tuan Rumah wajib diisi',

            // ALAMAT
            'alamat.required' => 'Alamat wajib diisi',

            // SEMESTER
            'semester.required' => 'Semester wajib dipilih',

            // TAHUN KKN
            'tahun_kkn.required' => 'Tahun KKN wajib diisi',
            'tahun_kkn.digits' => 'Tahun KKN harus 4 digit',

            // NOMOR TELEPON
            'nomor_telepon.required' => 'Nomor Telepon wajib diisi',
            'nomor_telepon.digits_between' => 'Nomor Telepon harus 10 sampai 15 digit',

            // KAPASITAS
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.integer' => 'Kapasitas harus angka',
            'kapasitas.min' => 'Kapasitas minimal 1',

            // LATITUDE
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.numeric' => 'Latitude harus angka',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',

            // LONGITUDE
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.numeric' => 'Longitude harus angka',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',

            // DPL
            'nik.required' => 'DPL wajib dipilih',

            // APL
            'nim.required' => 'APL wajib dipilih',
        ]);

        $data = Kelompok::where('id_periode', $periode_id)
            ->where('id_kelompok', $id)
            ->firstOrFail();

        $data = Kelompok::where('id_periode', $periode_id)
            ->where('id_kelompok', $id)
            ->firstOrFail();

        // ==========================
        // HANDLE TUAN RUMAH ANTI DUPLIKAT
        // ==========================

        $nama_tuan_rumah = trim($request->id_tuan_rumah);

        // cek apakah nama sudah ada
        $tuanRumahExist = DB::table('tuan_rumah')
            ->whereRaw(
                'LOWER(nama_tuan_rumah) = ?',
                [strtolower($nama_tuan_rumah)]
            )
            ->first();

        if ($tuanRumahExist) {

            // pakai ID yang sudah ada
            $id_tuan_rumah = $tuanRumahExist->id_tuan_rumah;

        } else {

            // pakai ID lama
            $id_tuan_rumah = $data->id_tuan_rumah;

            // update data lama
            DB::table('tuan_rumah')
                ->where('id_tuan_rumah', $id_tuan_rumah)
                ->update([

                    'nama_tuan_rumah' => $nama_tuan_rumah,

                    'dusun' => $request->dusun,
                    'desa' => $request->desa,
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'nama_kecamatan' => $request->nama_kecamatan,
                    'faskes' => $request->faskes
                ]);
        }

        try {

            $data->update([
                'nomor_kelompok' => $request->nomor_kelompok,
                'desa' => $request->desa,
                'dusun' => $request->dusun,
                'nama_dukuh' => $request->nama_dukuh,
                'id_tuan_rumah' => $id_tuan_rumah,
                'nomor_telepon' => $request->nomor_telepon,
                'alamat' => $request->alamat,
                'faskes' => $request->faskes,
                'kapasitas' => $request->kapasitas,
                'semester' => $request->semester,
                'tahun_kkn' => $request->tahun_kkn,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'nama_kecamatan' => $request->nama_kecamatan,
                'nik' => $request->nik,
                'nim' => $request->nim
            ]);

            session([
                'retain_kelompok' => [
                    'nama_kecamatan' => $request->nama_kecamatan,
                    'desa' => $request->desa,
                    'faskes' => $request->faskes,
                    'kapasitas' => $request->kapasitas,
                    'semester' => $request->semester,
                    'tahun_kkn' => $request->tahun_kkn,
                ]
            ]);

            return redirect('/kelompok')
                ->with('success', 'Data kelompok berhasil diupdate');

        } catch (\Exception $e) {

            return back()
                ->withErrors([
                    'error' => 'Gagal update data'
                ])
                ->withInput();
        }
    }

    public function generate(Request $request)
    {
        $this->logAktivitas('Generate', 'Randomisasi kelompok');
        $data = json_decode($request->data, true);

        if (!$data) {
            return redirect('/import')->with('error', 'Silakan upload ulang');
        }

        $periode_id = session('periode_id')
            ?? request('periode_id')
            ?? Periode::where('status_publish', 1)->value('id_periode');

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $kelompokList = Kelompok::where('id_periode', $periode_id)->get();
        $result = [];

        foreach ($data as $peserta) {
            // =========================
// NORMALISASI GENDER
// =========================

            $gender = strtolower(trim(
                $peserta['gender']
                ?? $peserta['Gender']
                ?? $peserta['jenis_kelamin']
                ?? $peserta['Jenis Kelamin']
                ?? $peserta['jk']
                ?? ''
            ));

            if (
                $gender == 'l' ||
                $gender == 'laki-laki' ||
                $gender == 'laki laki' ||
                $gender == 'pria'
            ) {
                $peserta['gender'] = 'Pria';
            } elseif (
                $gender == 'p' ||
                $gender == 'perempuan' ||
                $gender == 'wanita'
            ) {
                $peserta['gender'] = 'Wanita';
            } else {
                $peserta['gender'] = null;
            }

            $kandidat = [];

            foreach ($kelompokList as $kelompok) {

                $jumlah = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->count();

                if ($jumlah >= $kelompok->kapasitas)
                    continue;

                $isKhusus = ($peserta['riwayat_penyakit'] == 1 || $peserta['berkebutuhan_khusus'] == 1);

                if ($isKhusus) {
                    $sudahAda = collect($result)
                        ->where('id_kelompok', $kelompok->id_kelompok)
                        ->where(function ($p) {
                            return $p['riwayat_penyakit'] == 1 || $p['berkebutuhan_khusus'] == 1;
                        })->count();

                    if ($sudahAda > 0)
                        continue;
                }

                // =========================
                // SOFT RULE SCORING
                // =========================
                $score = 0;

                // =========================
                // PRIORITAS FASKES
                // peserta khusus diprioritaskan
                // ke kelompok yang punya faskes
                // =========================
                if (
                    $isKhusus &&
                    $kelompok->faskes == 1
                ) {
                    $score += 5;
                }

                // =========================
                // SEBARAN PRODI
                // jika belum ada prodi sama
                // maka tambah score
                // =========================
                $jumlahProdiSama = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('prodi', $peserta['prodi'])
                    ->count();

                if ($jumlahProdiSama == 0) {
                    $score += 2;
                }

                // =========================
                // SEBARAN BAHASA JAWA
                // minimal ada 1 orang
                // bisa bahasa jawa tiap kelompok
                // =========================
                $jumlahBisaJawa = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('bahasa_jawa', 1)
                    ->count();

                if (
                    $peserta['bahasa_jawa'] == 1 &&
                    $jumlahBisaJawa == 0
                ) {
                    $score += 3;
                }

                // =========================
                // KESEIMBANGAN GENDER
                // =========================
                $laki = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('gender', 'Pria')
                    ->count();

                $perempuan = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('gender', 'Wanita')
                    ->count();

                // peserta laki
                if (
                    $peserta['gender'] == 'Pria' &&
                    $laki <= $perempuan
                ) {
                    $score += 1;
                }

                // peserta perempuan
                if (
                    $peserta['gender'] == 'Wanita' &&
                    $perempuan <= $laki
                ) {
                    $score += 1;
                }

                // =========================
                // SIMPAN KANDIDAT + SCORE
                // =========================
                $kandidat[] = [
                    'kelompok' => $kelompok,
                    'score' => $score
                ];
            }

            if (count($kandidat) == 0) {
                $result[] = [
                    'nim' => $peserta['nim'],
                    'nama' => $peserta['nama'],
                    'prodi' => $peserta['prodi'],
                    'gender' => $peserta['gender'],
                    'bahasa_jawa' => $peserta['bahasa_jawa'],
                    'riwayat_penyakit' => $peserta['riwayat_penyakit'],
                    'berkebutuhan_khusus' => $peserta['berkebutuhan_khusus'],
                    'id_kelompok' => null,
                    'nomor_kelompok' => null,
                    'status' => 'melanggar_rule',
                ];
                continue;
            }

            // =========================
            // URUTKAN BERDASARKAN SCORE
            // SCORE TERBESAR DIPILIH
            // =========================
            $kandidat = collect($kandidat)
                ->sortByDesc('score')
                ->values();

            // ambil kandidat terbaik
            $pilih = $kandidat[0]['kelompok'];

            $result[] = [
                'nim' => $peserta['nim'],
                'nama' => $peserta['nama'],
                'prodi' => $peserta['prodi'],
                'gender' => $peserta['gender'],
                'bahasa_jawa' => $peserta['bahasa_jawa'],
                'riwayat_penyakit' => $peserta['riwayat_penyakit'],
                'berkebutuhan_khusus' => $peserta['berkebutuhan_khusus'],
                'id_kelompok' => $pilih->id_kelompok,
                'nomor_kelompok' => $pilih->nomor_kelompok,
                'status' => 'ok',
            ];
        }

        $melanggar = collect($result)->where('status', 'melanggar_rule')->count();

        session([
            'hasil_generate_' . $periode_id => $result
        ]);

        if ($melanggar > 0) {
            return redirect('/randomisasi')
                ->with('warning', 'Tidak ada kelompok yang memenuhi aturan sistem.');
        }

        return redirect('/randomisasi');
    }

    public function randomisasi()
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();

        $data = session('hasil_generate_' . $periode_id);

        if (!$data) {
            return redirect('/import')->withErrors([
                'error' => 'Silakan upload dan generate ulang data peserta'
            ]);
        }

        return view('kelompok.randomisasi', compact('data'));
    }

    public function simpanHasil()
    {
        $this->setPeriodeSession();

        $periode_id = session('periode_id')
            ?? request('periode_id');

        if (!$periode_id) {
            return back()->withErrors(['error' => 'Periode tidak ditemukan']);
        }

        $this->logAktivitas('Simpan Hasil', 'Menyimpan hasil pembagian kelompok');
        $data = session('hasil_generate_' . $periode_id);

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        if (!$data) {
            return back()->withErrors(['error' => 'Data tidak ditemukan']);
        }

        $raw_periode = request('periode_id') ?? session('periode_id');

        $periode_id = (int) preg_replace('/[^0-9]/', '', $raw_periode);

        if (!$periode_id) {
            dd('PERIODE ERROR:', $raw_periode);
        }

        $periode_id = (int) (
            session('periode_id')
            ?? request('periode_id')
        );

        Peserta::where('id_periode', $periode_id)->delete();

        foreach ($data as $row) {
            Peserta::updateOrCreate(
                [
                    'nim' => $row['nim'],
                    'id_periode' => $periode_id
                ],

                [
                    'nama' => $row['nama'],
                    'prodi' => $row['prodi'],
                    'gender' => $row['gender'],
                    'bahasa_jawa' => $row['bahasa_jawa'],
                    'riwayat_penyakit' => $row['riwayat_penyakit'],
                    'berkebutuhan_khusus' => $row['berkebutuhan_khusus'],
                    'id_kelompok' => $row['id_kelompok'] ?? null,
                ]
            );

        }

        return redirect()->route('hasil.pembagian');
    }


    public function hasilPembagian(Request $request)
    {
        $periode_id = $request->periode_id
            ?? session('periode_id')
            ?? Periode::latest()->value('id_periode');

        if (!$periode_id) {
            return redirect('/dashboard')
                ->with('error', 'Belum ada data periode!');
        }

        session(['periode_id' => $periode_id]);

        $peserta = Peserta::with(['kelompok.dpl', 'kelompok.apl'])
            ->where('id_periode', $periode_id)
            ->get();

        $kelompok = $peserta->whereNotNull('id_kelompok')
            ->groupBy('id_kelompok')
            ->sortBy(function ($group) {
                return optional($group->first()->kelompok)->nomor_kelompok ?? 0;
            });
        $belum = $peserta->whereNull('id_kelompok');

        $kelompokList = Kelompok::where('id_periode', $periode_id)->get();
        $dplList = Dpl::where('id_periode', $periode_id)
            ->select('nik', 'nama', 'no_telp')
            ->distinct()
            ->get();

        $aplList = Apl::where('id_periode', $periode_id)
            ->select('nim', 'nama', 'no_telp')
            ->distinct()
            ->get();

        $status = Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        return view('kelompok.hasil_pembagian', compact(
            'kelompok',
            'belum',
            'kelompokList',
            'dplList',
            'aplList',
            'status'
        ));
    }

    public function resetPembagian()
    {
        $this->logAktivitas('Reset Pembagian', 'Semua peserta dihapus dari kelompok');

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // RESET SEMUA PESERTA BERDASARKAN PERIODE (JANGAN HAPUS DPL & APL)
        Peserta::where('id_periode', $periode_id)
            ->update([
                'id_kelompok' => null
            ]);

        // HAPUS SESSION RANDOMISASI
        session()->forget('hasil_generate_' . $periode_id);

        return redirect()->route('hasil.pembagian')
            ->with('success', 'Pembagian berhasil direset');
    }

    public function exportExcel($periode_id)
    {
        $this->logAktivitas('Export Excel dari Halaman Hasil Pembagian');

        $periode_id = request('periode_id')
            ?? session('periode_id');

        $periode = Periode::find($periode_id);

        $namaFile =
            'hasil_pembagian_kkn_reguler_' .
            ($periode->tahun_kkn ?? date('Y')) .
            '.xlsx';

        return Excel::download(
            new HasilPembagianExport($periode_id),
            $namaFile
        );
    }

    public function exportPDF($periode_id)
    {
        $this->logAktivitas('Export PDF dari Halaman Hasil Pembagian');

        $periode_id = request('periode_id')
            ?? session('periode_id');

        $data = Peserta::with(['kelompok.dpl', 'kelompok.apl'])
            ->whereHas('kelompok', function ($q) use ($periode_id) {
                $q->where('id_periode', $periode_id);
            })
            ->get();

        $grouped = $data->groupBy(function ($p) {
            return $p->kelompok->nomor_kelompok ?? 'Tanpa Kelompok';
        });

        $pdf = Pdf::loadView('kelompok.export_pdf', compact('grouped'))
            ->setPaper('a4', 'landscape');

        $periode = Periode::find($periode_id);

        $namaFile =
            'hasil_pembagian_kkn_reguler_' .
            ($periode->tahun_kkn ?? date('Y')) .
            '.pdf';

        return $pdf->download($namaFile);
    }

    public function publish(Request $request)
    {
        $this->logAktivitas('Publish', 'Hasil pembagian dipublish');

        DB::beginTransaction();

        try {

            $periode_id = $request->periode_id;

            // =========================
            // AMBIL SEMUA PESERTA PERIODE
            // =========================

            $peserta = \App\Models\Peserta::where('id_periode', $periode_id)
                ->get();

            // =========================
            // CEK ADA YANG BELUM KELOMPOK?
            // =========================

            $belum = $peserta->whereNull('id_kelompok');

            if ($belum->count() > 0) {

                DB::rollback();

                return back()->with(
                    'error',
                    'Masih terdapat peserta yang belum mendapat kelompok.'
                );
            }

            // =========================
            // CEK DUPLIKAT
            // =========================

            $double = $peserta
                ->groupBy(function ($p) {
                    return $p->nim . '_' . $p->id_periode;
                })
                ->filter(function ($p) {
                    return $p->count() > 1;
                });

            if ($double->count() > 0) {

                DB::rollback();

                return back()->with(
                    'error',
                    'Terdapat data peserta ganda.'
                );
            }

            // =========================
            // CEK STATUS PUBLISH
            // =========================

            $status = Periode::where('id_periode', $periode_id)
                ->value('status_publish');

            if ($status == 1) {

                DB::rollback();

                return back()->with(
                    'error',
                    'Data sudah dipublish sebelumnya!'
                );
            }

            // =========================
            // UPDATE STATUS PUBLISH
            // =========================

            Periode::where('id_periode', $periode_id)
                ->update([
                    'status_publish' => 1
                ]);

            DB::commit();

            return back()->with(
                'success',
                'Hasil pembagian berhasil dipublish!'
            );

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with(
                'error',
                'Terjadi kesalahan saat publish: ' . $e->getMessage()
            );
        }
    }
    private function logAktivitas($aksi, $deskripsi = null)
    {
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => $deskripsi
                ? $aksi . ' - ' . $deskripsi
                : $aksi
        ]);
    }

}