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
            ?? Periode::where('status_publish', 1)->value('id_periode');
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

        $kelompok = Kelompok::where('id_periode', $periode_id)->get();

        return view('kelompok.index', compact(
            'kelompok',
            'periodes',
            'periode_id'
        ));
    }

    public function create()
    {
        return view('kelompok.create');
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

        $request->validate([
            'nomor_kelompok' => 'required|integer|min:1',
            'desa' => 'required',
            'dusun' => 'required',
            'nama_dukuh' => 'required',
            'nama_tuan_rumah' => 'required',
            'nomor_telepon' => 'required|digits_between:10,15',
            'alamat' => 'required',
            'faskes' => 'required|in:Ya,Tidak',
            'kapasitas' => 'required|integer|min:1',
            'semester' => 'required',
            'tahun_kkn' => 'required|digits:4',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.numeric' => 'Latitude harus angka',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',

            'longitude.required' => 'Longitude wajib diisi',
            'longitude.numeric' => 'Longitude harus angka',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
        ]);

        try {

            $id_periode = $this->getPeriodeId();
            Kelompok::create([
                'nomor_kelompok' => $request->nomor_kelompok,
                'desa' => $request->desa,
                'dusun' => $request->dusun,
                'nama_dukuh' => $request->nama_dukuh,
                'nama_tuan_rumah' => $request->nama_tuan_rumah,
                'nomor_telepon' => $request->nomor_telepon,
                'alamat' => $request->alamat,
                'faskes' => $request->faskes == 'Ya' ? 1 : 0,
                'kapasitas' => $request->kapasitas,
                'semester' => $request->semester,
                'tahun_kkn' => $request->tahun_kkn,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'id_periode' => $id_periode
            ]);

            return redirect('/kelompok')->with('success', 'Data kelompok berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data'])->withInput();
        }
    }

    public function edit($id)
    {
        $data = Kelompok::findOrFail($id);
        return view('kelompok.edit', compact('data'));
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

        $request->validate([
            'nomor_kelompok' => 'required|integer|min:1',
            'desa' => 'required|string|max:255',
            'dusun' => 'required|string|max:255',
            'nama_dukuh' => 'required|string|max:255',
            'nama_tuan_rumah' => 'required|string|max:255',
            'nomor_telepon' => 'required|digits_between:10,15',
            'alamat' => 'required|string|max:255',
            'faskes' => 'required|in:Ya,Tidak',
            'kapasitas' => 'required|integer|min:1',
            'semester' => 'required|in:Gasal,Genap',
            'tahun_kkn' => 'required|digits:4',

            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.numeric' => 'Latitude harus angka',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',

            'longitude.required' => 'Longitude wajib diisi',
            'longitude.numeric' => 'Longitude harus angka',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',

            'nomor_kelompok.numeric' => 'Nomor kelompok harus angka',
            'kapasitas.numeric' => 'Kapasitas harus angka',
        ]);

        $data = Kelompok::findOrFail($id);

        try {
            $data->update([
                'nomor_kelompok' => $request->nomor_kelompok,
                'desa' => $request->desa,
                'dusun' => $request->dusun,
                'nama_dukuh' => $request->nama_dukuh,
                'nama_tuan_rumah' => $request->nama_tuan_rumah,
                'nomor_telepon' => $request->nomor_telepon,
                'alamat' => $request->alamat,
                'faskes' => $request->faskes == 'Ya' ? 1 : 0,
                'kapasitas' => $request->kapasitas,
                'semester' => $request->semester,
                'tahun_kkn' => $request->tahun_kkn,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            return redirect('/kelompok')->with('success', 'Data kelompok berhasil diupdate');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update data'])->withInput();
        }
    }

    public function delete($id)
    {
        $this->logAktivitas(
            'Hapus Kelompok',
            "Menghapus kelompok ID $id"
        );

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        Kelompok::destroy($id);
        return redirect('/kelompok');
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

                $kandidat[] = $kelompok;
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

            $pilih = $kandidat[array_rand($kandidat)];

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

        session(['hasil_generate' => $result]);

        if ($melanggar > 0) {
            return redirect('/randomisasi')
                ->with('warning', 'Tidak ada kelompok yang memenuhi aturan sistem.');
        }

        return redirect('/randomisasi');
    }

    public function randomisasi()
    {
        $this->setPeriodeSession();
        $data = session('hasil_generate');

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
        $data = session('hasil_generate');

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
            ->whereHas('kelompok', function ($q) use ($periode_id) {
                $q->where('id_periode', $periode_id);
            })
            ->get();

        $kelompok = $peserta->whereNotNull('id_kelompok')->groupBy('id_kelompok');
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

    public function assignDpl(Request $request)
    {
        $this->logAktivitas(
            'Assign DPL',
            "Menentukan DPL ke kelompok {$request->id_kelompok}"
        );

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->validate([
            'id_kelompok' => 'required',
            'nik' => 'required'
        ]);

        $kelompok = Kelompok::find($request->id_kelompok);

        if (!$kelompok) {
            return back()->with('error', 'Kelompok tidak ditemukan');
        }

        $dpl = \App\Models\Dpl::where('nik', $request->nik)->first();

        if (!$dpl) {
            return back()->with('error', 'DPL tidak ditemukan');
        }

        $kelompok->update([
            'nik' => $request->nik
        ]);

        return back()->with('success', 'DPL berhasil ditambahkan');
    }

    public function assignApl(Request $request)
    {
        $this->logAktivitas(
            'Assign APL',
            "Menentukan APL ke kelompok {$request->id_kelompok}"
        );

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->validate([
            'id_kelompok' => 'required',
            'nim' => 'required'
        ]);

        $kelompok = Kelompok::find($request->id_kelompok);

        if (!$kelompok) {
            return back()->with('error', 'Kelompok tidak ditemukan');
        }

        $apl = \App\Models\Apl::where('nim', $request->nim)->first();

        if (!$apl) {
            return back()->with('error', 'APL tidak ditemukan');
        }

        $kelompok->update([
            'nim' => $request->nim
        ]);

        return back()->with('success', 'APL berhasil ditambahkan');
    }
    public function resetPembagian()
    {
        $this->logAktivitas('Reset Pembagian', 'Semua peserta dihapus dari kelompok');

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // RESET PESERTA
        Peserta::whereHas('kelompok', function ($q) use ($periode_id) {
            $q->where('id_periode', $periode_id);
        })->update([
                    'id_kelompok' => null
                ]);

        // 🔥 RESET DPL & APL DI KELOMPOK
        Kelompok::where('id_periode', $periode_id)->update([
            'nik' => null,
            'nim' => null
        ]);


        return redirect()->route('hasil.pembagian')
            ->with('success', 'Pembagian berhasil direset');
    }

    public function resetTotal()
    {
        $this->logAktivitas('Reset Total', 'Semua data peserta dihapus');

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // HAPUS PESERTA
        Peserta::whereHas('kelompok', function ($q) use ($periode_id) {
            $q->where('id_periode', $periode_id);
        })->delete();


        // 🔥 RESET DPL & APL
        Kelompok::where('id_periode', $periode_id)->update([
            'nik' => null,
            'nim' => null
        ]);

        return redirect()->route('hasil.pembagian')
            ->with('success', 'Semua data peserta dihapus');
    }

    public function exportExcel($periode_id)
    {
        $this->logAktivitas('Export Excel dari Halaman Hasil Pembagian');

        $periode_id = request('periode_id')
            ?? session('periode_id');

        return Excel::download(new PesertaExport($periode_id), 'laporan.xlsx');
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

        return $pdf->download('hasil_pembagian.pdf');
    }

    public function publish(Request $request)
    {
        $this->logAktivitas('Publish', 'Hasil pembagian dipublish');

        DB::beginTransaction();

        try {

            $periode_id = $request->periode_id;

            $peserta = \App\Models\Peserta::whereHas('kelompok', function ($q) use ($periode_id) {
                $q->where('id_periode', $periode_id);
            })->get();

            $belum = $peserta->whereNull('id_kelompok');

            if ($belum->count() > 0) {
                DB::rollback();
                return back()->with('error', 'Masih terdapat peserta yang belum mendapat kelompok.');
            }

            $double = $peserta->groupBy('nim')->filter(function ($p) {
                return $p->count() > 1;
            });

            if ($double->count() > 0) {
                DB::rollback();
                return back()->with('error', 'Terdapat data peserta ganda.');
            }

            $status = Periode::where('id_periode', $periode_id)
                ->value('status_publish');

            if ($status == 1) {
                DB::rollback();
                return back()->with('error', 'Data sudah dipublish sebelumnya!');
            }

            $warning = [];

            $kelompok = \App\Models\Kelompok::with('peserta')
                ->where('id_periode', $periode_id)
                ->get();

            foreach ($kelompok as $k) {
                $jumlah = $k->peserta->count();

                if ($jumlah > $k->kapasitas) {
                    $warning[] = "Kelompok K{$k->nomor_kelompok} melebihi kapasitas";
                }
            }

            \App\Models\Periode::where('id_periode', $periode_id)
                ->update(['status_publish' => 1]);

            DB::commit();

            if (!empty($warning)) {
                return back()->with('warning', implode(', ', $warning));
            }

            return redirect()->back()->with('success', 'Hasil pembagian berhasil dipublish!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan saat publish.');
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