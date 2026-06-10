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
use Illuminate\Validation\Rule;
use App\Models\Periode;
use App\Exports\HasilPembagianExport;
use App\Models\LogActivity;

class KelompokController extends Controller
{
    // Menyimpan periode yang dipilih ke dalam session
    private function setPeriodeSession()
    {
        if (request('periode_id')) {
            session(['periode_id' => request('periode_id')]);
        }
    }

    // Mengambil ID periode aktif dari session atau periode yang sedang berjalan
    private function getPeriodeId()
    {
        return session('periode_id')
            ?? request('periode_id')
            ?? Periode::where('status', 'berjalan')
                ->value('id_periode');
    }

    // Memeriksa apakah periode sudah dipublish sehingga data tidak dapat diubah
    private function checkPublishLock($periode_id)
    {
        $status = \App\Models\Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        if ($status == 1) {
            return back()->with('error', 'Periode sudah dipublish, data tidak bisa diubah!');
        }

        return null;
    }

    // Menampilkan halaman daftar kelompok berdasarkan periode yang dipilih
    public function index(Request $request)
    {
        if ($request->periode_id) {
            session(['periode_id' => $request->periode_id]);
        }

        // HANYA PERIODE BERJALAN
        $periodes = Periode::where('status', 'berjalan')
            ->latest()
            ->get();

        // AMBIL PERIODE TERPILIH
        $periode_id = session('periode_id');

        if (!$periode_id) {

            $periode_id = Periode::where('status', 'berjalan')
                ->value('id_periode');

            session(['periode_id' => $periode_id]);
        }

        // JIKA BELUM ADA -> AMBIL YANG TERAKHIR BERJALAN
        if (!$periode_id) {

            $periode_id = Periode::where('status', 'berjalan')
                ->latest()
                ->value('id_periode');

            session(['periode_id' => $periode_id]);
        }

        $kelompok = Kelompok::with(['tuanRumah', 'dpl', 'apl'])
            ->where('id_periode', $periode_id)
            ->get();

        return view('kelompok.index', compact(
            'kelompok',
            'periodes',
            'periode_id'
        ));
    }

    // Menampilkan halaman tambah kelompok dan menyiapkan data pendukung
    public function create()
    {
        $periode_id = $this->getPeriodeId();

        $dpl = Dpl::where('id_periode', $periode_id)->get();
        $apl = Apl::where('id_periode', $periode_id)->get();

        $tuan_rumah = DB::table('tuan_rumah')->get();

        // =========================
        // AUTO NOMOR KELOMPOK
        // =========================
        $lastKelompok = Kelompok::where('id_periode', $periode_id)
            ->max('nomor_kelompok');

        $nextKelompok = ($lastKelompok ?? 0) + 1;

        return view('kelompok.create', compact(
            'dpl',
            'apl',
            'tuan_rumah',
            'nextKelompok'
        ));
    }

    // Mengambil data kelompok berdasarkan dusun untuk kebutuhan autofill form
    public function getDusun($dusun)
    {
        // ambil data kelompok berdasarkan dusun
        $data = Kelompok::where('dusun', $dusun)
            ->where('id_periode', $this->getPeriodeId())
            ->first();

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

    // Mengambil data tuan rumah berdasarkan nama yang dipilih untuk autofill form
    public function getTuanRumah($nama)
    {
        $tuan = DB::table('tuan_rumah')
            ->where('nama_tuan_rumah', $nama)
            ->first();

        if (!$tuan) {
            return response()->json(null);
        }

        // ambil kelompok terakhir berdasarkan dusun/desa
        $kelompok = Kelompok::where('dusun', $tuan->dusun)
            ->where('desa', $tuan->desa)
            ->where('id_periode', $this->getPeriodeId())
            ->latest('id_kelompok')
            ->first();

        return response()->json([

            // =========================
            // DATA TUAN RUMAH
            // =========================
            'nomor_telepon' => $tuan->nomor_telepon,
            'alamat' => $tuan->alamat,
            'latitude' => $tuan->latitude,
            'longitude' => $tuan->longitude,

            // =========================
            // DATA LOKASI
            // =========================
            'desa' => $tuan->desa,
            'dusun' => $tuan->dusun,
            'nama_kecamatan' => $tuan->nama_kecamatan,

            // =========================
            // DATA KELOMPOK
            // =========================
            'kapasitas' => $kelompok->kapasitas ?? null,
            'semester' => $kelompok->semester ?? null,
            'tahun_kkn' => $kelompok->tahun_kkn ?? null,
            'faskes' => $kelompok->faskes ?? null,
        ]);
    }

    // Menyimpan data kelompok baru ke database
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

        // VALIDASI
        $validated = $request->validate([
            'nomor_kelompok' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('kelompok')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
            ],
            'desa' => 'required',
            'dusun' => 'required',
            'nama_dukuh' => 'required',
            'tuan_rumah' => 'required|string|max:255',
            'nomor_telepon' => 'required|digits_between:10,15',
            'alamat' => 'required',
            'faskes' => 'required|in:0,1',
            'kapasitas' => 'required|integer|min:1',
            'semester' => 'required',
            'tahun_kkn' => 'required|digits:4',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'nama_kecamatan' => 'required',
            'nik' => 'required',
            'nim' => 'required',
        ], [
            // NOMOR KELOMPOK
            'nomor_kelompok.required' => 'Nomor Kelompok wajib diisi',
            'nomor_kelompok.integer' => 'Nomor Kelompok harus angka',
            'nomor_kelompok.min' => 'Nomor Kelompok minimal 1',
            'nomor_kelompok.unique' => 'Nomor kelompok sudah digunakan pada periode ini',

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
            'latitude.numeric' => 'Latitude harus angka',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',

            // LONGITUDE
            'longitude.numeric' => 'Longitude harus angka',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',

            // DPL
            'nik.required' => 'DPL wajib dipilih',

            // APL
            'nim.required' => 'APL wajib dipilih',
        ]);

        // HANDLE TUAN RUMAH (FIX AMAN)

        // Coba cek apakah input adalah ID yang valid
        $tuanById = DB::table('tuan_rumah')
            ->where('id_tuan_rumah', $request->id_tuan_rumah)
            ->first();

        if ($tuanById) {

            //  ARTINYA PILIH DARI DROPDOWN (ID VALID)
            $id_tuan_rumah = $tuanById->id_tuan_rumah;

        } else {

            // ARTINYA INPUT MANUAL (NAMA)
            $nama = trim($request->tuan_rumah);

            // CEK DUPLIKAT (CASE INSENSITIVE)
            $tuan = DB::table('tuan_rumah')
                ->whereRaw('LOWER(nama_tuan_rumah) = ?', [strtolower($nama)])
                ->first();

            if (!$tuan) {

                // INSERT BARU
                $id_tuan_rumah = DB::table('tuan_rumah')->insertGetId([

                    'nama_tuan_rumah' => $nama,

                    // LOKASI
                    'dusun' => $request->dusun,
                    'desa' => $request->desa,
                    'nama_kecamatan' => $request->nama_kecamatan,

                    // TUAN RUMAH
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,

                    // DATA KELOMPOK
                    'kapasitas' => $request->kapasitas,
                    'semester' => $request->semester,
                    'tahun_kkn' => $request->tahun_kkn,
                    'faskes' => $request->faskes,

                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            } else {

                // SUDAH ADA → PAKAI YANG LAMA
                $id_tuan_rumah = $tuan->id_tuan_rumah;
            }
        }

        // UPDATE hanya kalau DATA SUDAH ADA
        if (isset($id_tuan_rumah)) {

            DB::table('tuan_rumah')
                ->where('id_tuan_rumah', $id_tuan_rumah)
                ->update([

                    // DATA LOKASI
                    'dusun' => $request->dusun,
                    'desa' => $request->desa,
                    'nama_kecamatan' => $request->nama_kecamatan,

                    // DATA TUAN RUMAH
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,

                    // DATA KELOMPOK
                    'kapasitas' => $request->kapasitas,
                    'semester' => $request->semester,
                    'tahun_kkn' => $request->tahun_kkn,
                    'faskes' => $request->faskes,

                    'updated_at' => now()

                ]);
        }

        try {

            $id_periode = $this->getPeriodeId();

            if (!$id_periode) {

                $id_periode = Periode::where('status', 'berjalan')
                    ->value('id_periode');
            }

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
            return back()->withErrors(['error' => 'Gagal menyimpan data']);
        }
    }

    // Menampilkan halaman edit data kelompok
    public function edit($id)
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $data = Kelompok::where('id_periode', $periode_id)
            ->where('id_kelompok', $id)
            ->firstOrFail();

        $dpl = Dpl::where('id_periode', $periode_id)->get();
        $apl = Apl::where('id_periode', $periode_id)->get();
        $tuan_rumah = DB::table('tuan_rumah')->get();

        return view('kelompok.edit', compact('data', 'dpl', 'apl', 'tuan_rumah'));
    }

    // Memperbarui data kelompok yang sudah ada
    public function update(Request $request, $id)
    {
        $this->logAktivitas(
            'Update Kelompok',
            "Update kelompok K{$request->nomor_kelompok}"
        );

        $periode_id = request('periode_id')
            ?? session('periode_id')
            ?? Periode::where('status', 'berjalan')->value('id_periode');

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $data = Kelompok::where('id_periode', $periode_id)
            ->where('id_kelompok', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'nomor_kelompok' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('kelompok')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
                    ->ignore($data->id_kelompok, 'id_kelompok')
            ],
            'desa' => 'required|string|max:255',
            'dusun' => 'required|string|max:255',
            'nama_dukuh' => 'required|string|max:255',
            'tuan_rumah' => 'required|string|max:255',
            'nomor_telepon' => 'required|digits_between:10,15',
            'alamat' => 'required|string|max:255',
            'faskes' => 'required|in:0,1',
            'kapasitas' => 'required|integer|min:1',
            'semester' => 'required|in:Gasal,Genap',
            'tahun_kkn' => 'required|digits:4',
            'nama_kecamatan' => 'required|string|max:255',
            'nik' => 'required',
            'nim' => 'required',

            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            // NOMOR KELOMPOK
            'nomor_kelompok.required' => 'Nomor Kelompok wajib diisi',
            'nomor_kelompok.integer' => 'Nomor Kelompok harus angka',
            'nomor_kelompok.min' => 'Nomor Kelompok minimal 1',
            'nomor_kelompok.unique' => 'Nomor kelompok sudah digunakan pada periode ini',

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
            'latitude.numeric' => 'Latitude harus angka',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',

            // LONGITUDE
            'longitude.numeric' => 'Longitude harus angka',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',

            // DPL
            'nik.required' => 'DPL wajib dipilih',

            // APL
            'nim.required' => 'APL wajib dipilih',
        ]);

        // ==========================
        // HANDLE TUAN RUMAH ANTI DUPLIKAT
        // ==========================

        // cek apakah input adalah ID dari datalist
        $tuanById = DB::table('tuan_rumah')
            ->where('id_tuan_rumah', $request->id_tuan_rumah)
            ->first();

        if ($tuanById) {

            // =====================================
            // PILIH DARI DATALIST
            // =====================================

            $id_tuan_rumah = $tuanById->id_tuan_rumah;

            // update data terbaru
            DB::table('tuan_rumah')
                ->where('id_tuan_rumah', $id_tuan_rumah)
                ->update([

                    // DATA LOKASI
                    'dusun' => $request->dusun,
                    'desa' => $request->desa,
                    'nama_kecamatan' => $request->nama_kecamatan,

                    // DATA TUAN RUMAH
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,

                    // DATA KELOMPOK
                    'kapasitas' => $request->kapasitas,
                    'semester' => $request->semester,
                    'tahun_kkn' => $request->tahun_kkn,
                    'faskes' => $request->faskes,

                    'updated_at' => now()

                ]);

        } else {

            // =====================================
            // INPUT NAMA BARU MANUAL
            // =====================================

            $nama_tuan_rumah = trim($request->tuan_rumah);

            // cek apakah nama sudah ada
            $tuanRumahExist = DB::table('tuan_rumah')
                ->whereRaw(
                    'LOWER(nama_tuan_rumah) = ?',
                    [strtolower($nama_tuan_rumah)]
                )
                ->first();

            if ($tuanRumahExist) {

                // pakai ID lama yang sudah ada
                $id_tuan_rumah = $tuanRumahExist->id_tuan_rumah;

            } else {

                // =====================================
                // BUAT DATA TUAN RUMAH BARU
                // =====================================

                $id_tuan_rumah = DB::table('tuan_rumah')
                    ->insertGetId([

                        'nama_tuan_rumah' => $nama_tuan_rumah,

                        // LOKASI
                        'dusun' => $request->dusun,
                        'desa' => $request->desa,
                        'nama_kecamatan' => $request->nama_kecamatan,

                        // TUAN RUMAH
                        'nomor_telepon' => $request->nomor_telepon,
                        'alamat' => $request->alamat,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,

                        // DATA KELOMPOK
                        'kapasitas' => $request->kapasitas,
                        'semester' => $request->semester,
                        'tahun_kkn' => $request->tahun_kkn,
                        'faskes' => $request->faskes,

                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }
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

    // Melakukan proses randomisasi peserta ke dalam kelompok berdasarkan aturan sistem
    public function generate(Request $request)
    {
        // Mengubah data JSON dari form menjadi array PHP
        $data = json_decode($request->data, true);

        // Jika data kosong atau gagal dibaca
        if (!$data) {
            return redirect('/import')->with('error', 'Silakan upload ulang');
        }

        // Menyimpan aktivitas generate ke log sistem
        $this->logAktivitas('Generate', 'Randomisasi kelompok');

        // Mengambil id periode dari session / request / periode berjalan
        $periode_id = session('periode_id')
            ?? request('periode_id')
            ?? Periode::where('status', 'berjalan')->value('id_periode');

        // Menggunakan helper getPeriodeId()
        $periode_id = $this->getPeriodeId();

        // Mengecek apakah periode sudah dipublish
        // Jika sudah publish maka data tidak boleh diubah
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // Mengambil seluruh kelompok pada periode aktif
        $kelompokList = Kelompok::where('id_periode', $periode_id)->get();

        // Array untuk menampung hasil pembagian peserta
        $result = [];

        // Memproses peserta satu per satu
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

            // Menampung kelompok yang memenuhi aturan
            $kandidat = [];

            // Membandingkan peserta dengan seluruh kelompok
            foreach ($kelompokList as $kelompok) {

                // Menghitung jumlah anggota sementara pada kelompok
                $jumlah = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->count();

                // Jika kelompok penuh maka dilewati
                if ($jumlah >= $kelompok->kapasitas)
                    continue;

                // Peserta dianggap khusus jika memiliki penyakit
                // atau berkebutuhan khusus
                $isKhusus = ($peserta['riwayat_penyakit'] == 1 || $peserta['berkebutuhan_khusus'] == 1);

                // Jika peserta khusus
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
                // Nilai awal kelompok 
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
                // Hitung jumlah prodi yang sama
                $jumlahProdiSama = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('prodi', $peserta['prodi'])
                    ->count();

                // Jika belum ada prodi tersebut
                if ($jumlahProdiSama == 0) {
                    $score += 2;
                }

                // =========================
                // SEBARAN BAHASA JAWA
                // minimal ada 1 orang
                // bisa bahasa jawa tiap kelompok
                // =========================
                // Menghitung jumlah anggota yang bisa bahasa Jawa
                $jumlahBisaJawa = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('bahasa_jawa', 1)
                    ->count();

                // Jika peserta bisa bahasa Jawa
                // dan kelompok belum memiliki anggota yang bisa bahasa Jawa
                if (
                    $peserta['bahasa_jawa'] == 1 &&
                    $jumlahBisaJawa == 0
                ) {
                    $score += 3;
                }

                // =========================
                // KESEIMBANGAN GENDER
                // =========================
                // Hitung jumlah pria
                $laki = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('gender', 'Pria')
                    ->count();

                // Hitung jumlah wanita
                $perempuan = collect($result)
                    ->where('id_kelompok', $kelompok->id_kelompok)
                    ->where('gender', 'Wanita')
                    ->count();

                // Jika peserta pria dan jumlah pria masih seimbang
                if (
                    $peserta['gender'] == 'Pria' &&
                    $laki <= $perempuan
                ) {
                    $score += 1;
                }

                // Jika peserta wanita dan jumlah wanita masih seimbang
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

            // Jika tidak ada kelompok yang lolos hard rule
            if (count($kandidat) == 0) {
                $result[] = [
                    'nim' => $peserta['nim'],
                    'nama' => $peserta['nama'],
                    'no_telp' => $peserta['no_telp'] ?? null,
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
            // =========================
            // Mengurutkan kandidat dari skor terbesar ke terkecil
            $kandidat = collect($kandidat)
                ->sortByDesc('score')
                ->values();

            // =========================
            // AMBIL SCORE TERTINGGI
            // =========================
            $maxScore = $kandidat->first()['score'];

            // =========================
            // AMBIL SEMUA KANDIDAT KELOMPOK
            // DENGAN SCORE TERTINGGI
            // =========================
            $terbaik = $kandidat
                ->where('score', $maxScore)
                ->values();

            // =========================
            // RANDOMISASI TERBATAS
            // HANYA PADA KANDIDAT
            // DENGAN SCORE TERTINGGI
            // =========================
            // Jika terdapat lebih dari satu kelompok
            // dengan skor tertinggi yang sama
            // Sistem memilih satu kelompok secara acak
            // hanya dari kandidat terbaik

            $pilih = $terbaik->random()['kelompok'];

            // Menyimpan hasil penempatan peserta
            $result[] = [
                'nim' => $peserta['nim'],
                'nama' => $peserta['nama'],
                'no_telp' => $peserta['no_telp'] ?? null,
                'prodi' => $peserta['prodi'],
                'gender' => $peserta['gender'],
                'bahasa_jawa' => $peserta['bahasa_jawa'],
                'riwayat_penyakit' => $peserta['riwayat_penyakit'],
                'berkebutuhan_khusus' => $peserta['berkebutuhan_khusus'],
                // Kelompok yang terpilih
                'id_kelompok' => $pilih->id_kelompok,
                // Nomor kelompok
                'nomor_kelompok' => $pilih->nomor_kelompok,
                'status' => 'ok',
            ];
        }

        // Menghitung jumlah peserta yang gagal ditempatkan
        $melanggar = collect($result)->where('status', 'melanggar_rule')->count();

        // Menyimpan hasil generate sementara ke session
        session([
            'hasil_generate_' . $periode_id => $result
        ]);

        // Jika masih ada peserta yang gagal ditempatkan
        if ($melanggar > 0) {
            return redirect('/randomisasi')
                ->with('warning', 'Tidak ada kelompok yang memenuhi aturan sistem.');
        }

        // Menampilkan hasil pembagian kelompok
        return redirect('/randomisasi');
    }

    // Menampilkan hasil randomisasi peserta sebelum disimpan
    public function randomisasi()
    {
        // Menyimpan periode yang dipilih ke dalam session
        $this->setPeriodeSession();

        // Mengambil ID periode aktif
        $periode_id = $this->getPeriodeId();

        // Mengambil hasil generate yang sebelumnya disimpan di session
        $data = session('hasil_generate_' . $periode_id);

        // Jika hasil generate belum ada
        if (!$data) {
            // Kembali ke halaman import dan tampilkan pesan error
            return redirect('/import')->withErrors([
                'error' => 'Silakan upload dan generate ulang data peserta'
            ]);
        }

        // Menampilkan halaman preview hasil randomisasi
        return view('kelompok.randomisasi', compact('data'));
    }

    // Menyimpan hasil randomisasi ke tabel peserta
    public function simpanHasil()
    {
        // Menyimpan periode aktif ke session
        $this->setPeriodeSession();

        // Mengambil ID periode dari session atau request
        $periode_id = session('periode_id')
            ?? request('periode_id');

        // Jika periode tidak ditemukan
        if (!$periode_id) {
            // Kembali ke halaman sebelumnya
            return back()->withErrors(['error' => 'Periode tidak ditemukan']);
        }

        // Menyimpan aktivitas pengguna ke tabel log
        $this->logAktivitas('Simpan Hasil', 'Menyimpan hasil pembagian kelompok');

        // Mengambil hasil randomisasi dari session
        $data = session('hasil_generate_' . $periode_id);

        // Jika periode sudah dipublish
        // maka data tidak boleh diubah lagi
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // Jika data hasil generate tidak ditemukan
        if (!$data) {
            return back()->withErrors(['error' => 'Data tidak ditemukan']);
        }

        // Mengambil nilai periode mentah
        $raw_periode = request('periode_id') ?? session('periode_id');

        // Menghapus karakter selain angka
        $periode_id = (int) preg_replace('/[^0-9]/', '', $raw_periode);

        // Debug jika periode gagal terbaca
        if (!$periode_id) {
            dd('PERIODE ERROR:', $raw_periode);
        }

        // Mengambil ulang periode aktif
        $periode_id = (int) (
            session('periode_id')
            ?? request('periode_id')
        );

        // Menghapus seluruh data peserta
        // pada periode yang sedang diproses
        Peserta::where('id_periode', $periode_id)->delete();

        // Menyimpan seluruh hasil randomisasi
        foreach ($data as $row) {
            Peserta::updateOrCreate(
                [
                    'nim' => $row['nim'],
                    'id_periode' => $periode_id
                ],

                [
                    'nama' => $row['nama'],
                    'no_telp' => $row['no_telp'] ?? null,
                    'prodi' => $row['prodi'],
                    'gender' => $row['gender'],
                    'bahasa_jawa' => $row['bahasa_jawa'],
                    'riwayat_penyakit' => $row['riwayat_penyakit'],
                    'berkebutuhan_khusus' => $row['berkebutuhan_khusus'],
                    'id_kelompok' => $row['id_kelompok'] ?? null,
                ]
            );

        }

        // Setelah semua data berhasil disimpan
        // pindah ke halaman hasil pembagian
        return redirect()->route('hasil.pembagian');
    }

    // Menampilkan hasil pembagian kelompok yang telah disimpan
    public function hasilPembagian(Request $request)
    {
        // Jika user memilih periode dari dropdown/filter
        if ($request->periode_id) {
            // Simpan periode yang dipilih ke session
            session(['periode_id' => $request->periode_id]);
        }

        // ======================================
        // AMBIL DAFTAR PERIODE BERSTATUS BERJALAN
        // ======================================
        $periodes = Periode::where('status', 'berjalan')
            ->latest()
            ->get();

        // Ambil periode aktif dari session
        $periode_id = session('periode_id');

        // ======================================
        // JIKA SESSION BELUM ADA
        // AMBIL PERIODE BERJALAN
        // ======================================
        if (!$periode_id) {

            $periode_id = Periode::where('status', 'berjalan')
                ->value('id_periode');

            // Simpan ke session
            session(['periode_id' => $periode_id]);
        }

        // ======================================
        // JIKA MASIH BELUM ADA
        // AMBIL PERIODE BERJALAN TERBARU
        // ======================================
        if (!$periode_id) {

            $periode_id = Periode::where('status', 'berjalan')
                ->latest()
                ->value('id_periode');

            session(['periode_id' => $periode_id]);
        }

        // ======================================
        // JIKA TETAP TIDAK ADA PERIODE
        // ======================================
        if (!$periode_id) {
            return redirect('/dashboard')
                ->with('error', 'Belum ada data periode!');
        }

        // ======================================
        // AMBIL DATA PESERTA
        // ======================================
        $peserta = Peserta::with(['kelompok.dpl', 'kelompok.apl'])
            ->where('id_periode', $periode_id)
            ->get();

        // ======================================
        // KELOMPOKKAN PESERTA BERDASARKAN
        // ID KELOMPOK
        // ======================================
        $kelompok = $peserta->whereNotNull('id_kelompok')
            ->groupBy('id_kelompok')
            ->sortBy(function ($group) {
                return optional($group->first()->kelompok)->nomor_kelompok ?? 0;
            });

        // ======================================
        // PESERTA YANG BELUM DAPAT KELOMPOK
        // ======================================
        $belum = $peserta->whereNull('id_kelompok');


        // ======================================
        // AMBIL DATA MASTER KELOMPOK
        // ======================================
        $kelompokList = Kelompok::where('id_periode', $periode_id)->get();

        // ======================================
        // AMBIL DATA DPL
        // ======================================

        $dplList = Dpl::where('id_periode', $periode_id)
            ->select('id_dpl', 'nama', 'no_telp')
            ->distinct()
            ->get();

        // ======================================
        // AMBIL DATA APL
        // ======================================
        $aplList = Apl::where('id_periode', $periode_id)
            ->select('id_apl', 'nama', 'no_telp')
            ->distinct()
            ->get();

        // ======================================
        // CEK STATUS PUBLISH
        // ======================================
        $status = Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        // ======================================
        // KIRIM SEMUA DATA KE VIEW
        // ======================================
        return view('kelompok.hasil_pembagian', compact(
            'kelompok',
            'belum',
            'kelompokList',
            'dplList',
            'aplList',
            'status',
            'periodes',
            'periode_id'
        ));
    }

    // Menghapus seluruh hasil pembagian kelompok pada periode yang dipilih
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

    // Mengekspor hasil pembagian kelompok ke file Excel
    public function exportExcel($periode_id)
    {
        $this->logAktivitas('Export Excel dari Halaman Hasil Pembagian');

        $periode_id = request('periode_id')
            ?? session('periode_id');

        // =========================
        // FILTER DPL & APL
        // =========================
        $dpl_id = request('dpl_id');
        $apl_id = request('apl_id');

        // konversi ID -> NIK
        $dpl_nik = null;
        if ($dpl_id) {
            $dpl_nik = Dpl::where('id_dpl', $dpl_id)->value('nik');
        }

        // konversi ID -> NIM
        $apl_nim = null;
        if ($apl_id) {
            $apl_nim = Apl::where('id_apl', $apl_id)->value('nim');
        }

        $periode = Periode::find($periode_id);

        $namaFile =
            'hasil_pembagian_kkn_reguler_' .
            ($periode->tahun_kkn ?? date('Y')) .
            '.xlsx';

        return Excel::download(
            new HasilPembagianExport(
                $periode_id,
                $dpl_nik,
                $apl_nim
            ),
            $namaFile
        );
    }

    // Mengekspor hasil pembagian kelompok ke file PDF
    public function exportPDF($periode_id)
    {
        $this->logAktivitas('Export PDF dari Halaman Hasil Pembagian');

        $periode_id = request('periode_id')
            ?? session('periode_id');

        // =========================
        // FILTER DPL & APL
        // =========================
        $dpl_id = request('dpl_id');
        $apl_id = request('apl_id');

        // konversi ID -> NIK
        $dpl_nik = null;
        if ($dpl_id) {
            $dpl_nik = Dpl::where('id_dpl', $dpl_id)->value('nik');
        }

        // konversi ID -> NIM
        $apl_nim = null;
        if ($apl_id) {
            $apl_nim = Apl::where('id_apl', $apl_id)->value('nim');
        }

        $data = Peserta::with([
            'kelompok.dpl',
            'kelompok.apl'
        ])

            ->whereHas('kelompok', function ($q) use ($periode_id, $dpl_nik, $apl_nim) {

                $q->where('id_periode', $periode_id);

                // FILTER DPL
                if ($dpl_nik) {
                    $q->where('nik', $dpl_nik);
                }

                //FILTER APL
                if ($apl_nim) {
                    $q->where('nim', $apl_nim);
                }
            })

            ->get();

        $grouped = $data->groupBy(function ($p) {
            return $p->kelompok->nomor_kelompok ?? 'Tanpa Kelompok';
        });

        $pdf = Pdf::loadView(
            'kelompok.export_pdf',
            compact('grouped')
        )->setPaper('a4', 'landscape');

        $periode = Periode::find($periode_id);

        $namaFile =
            'hasil_pembagian_kkn_reguler_' .
            ($periode->tahun_kkn ?? date('Y')) .
            '.pdf';

        return $pdf->download($namaFile);
    }

    // Mempublikasikan hasil pembagian kelompok agar dapat diakses peserta
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

    // Membatalkan publikasi hasil pembagian kelompok
    public function unpublish(Request $request)
    {
        $this->logAktivitas('Unpublish', 'Hasil pembagian diunpublish');

        DB::beginTransaction();

        try {

            $periode_id = $request->periode_id;

            // =========================
            // CEK STATUS PUBLISH
            // =========================

            $status = Periode::where('id_periode', $periode_id)
                ->value('status_publish');

            if ($status == 0) {

                DB::rollback();

                return back()->with(
                    'error',
                    'Data belum dipublish!'
                );
            }

            // =========================
            // UPDATE STATUS PUBLISH
            // =========================

            Periode::where('id_periode', $periode_id)
                ->update([
                    'status_publish' => 0
                ]);

            DB::commit();

            return back()->with(
                'success',
                'Hasil pembagian berhasil diunpublish! Data dapat diubah kembali.'
            );

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with(
                'error',
                'Terjadi kesalahan saat unpublish: ' . $e->getMessage()
            );
        }
    }

    // Menghapus data kelompok berdasarkan ID kelompok
    public function delete($id)
    {
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $kelompok = Kelompok::where('id_kelompok', $id)
            ->where('id_periode', $periode_id)
            ->firstOrFail();

        $nomor_kelompok = $kelompok->nomor_kelompok;
        $kelompok->delete();

        // LOG ACTIVITY
        $this->logAktivitas(
            'Hapus Kelompok',
            "Menghapus kelompok K{$nomor_kelompok}"
        );

        return redirect('/kelompok')->with('success', 'Data kelompok berhasil dihapus');
    }

    // Menyimpan aktivitas pengguna ke dalam log sistem
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