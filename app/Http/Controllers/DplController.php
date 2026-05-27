<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Kelompok;
use App\Models\Periode;
use App\Models\Dpl;
use App\Models\LogActivity;

class DplController extends Controller
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

    private function validateSingleActivePeriode()
    {
        $count = \App\Models\Periode::where('status_publish', 1)->count();

        if ($count > 1) {
            return redirect('/dashboard')->with('error', 'Terdapat lebih dari 1 periode aktif! Silakan hubungi admin.');
        }

        return null;
    }

    public function index()
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();

        $data = Dpl::where('id_periode', $periode_id)->get();

        return view('dpl.index', compact('data'));
    }

    public function create()
    {
        $this->setPeriodeSession();

        $dplList = Dpl::select('nama')->distinct()->get();

        return view('dpl.create', compact('dplList'));
    }

    public function store(Request $request)
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->merge([
            'nik' => preg_replace('/[^0-9]/', '', $request->nik),
            'nidn' => preg_replace('/[^0-9]/', '', $request->nidn) ?: null,
            'no_telp' => preg_replace('/[^0-9]/', '', $request->no_telp),
            'id_periode' => $periode_id
        ]);

        $request->validate([
            'nik' => [
                'required',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id)),
            ],
            'nama' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id)),
            ],
            'no_telp' => 'required|digits_between:10,15',
            'prodi' => 'required|string|min:2|max:100',
            'fakultas' => 'required|string|min:2|max:100',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah digunakan pada periode ini',

            'nama.required' => 'Nama wajib diisi',

            'prodi.required' => 'Program studi wajib diisi',
            'prodi.min' => 'Program studi minimal 2 karakter',
            'prodi.max' => 'Program studi maksimal 100 karakter',

            'fakultas.required' => 'Fakultas wajib diisi',
            'fakultas.min' => 'Fakultas minimal 2 karakter',
            'fakultas.max' => 'Fakultas maksimal 100 karakter',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus mengandung @',
            'email.unique' => 'Email sudah digunakan pada periode ini',

            'no_telp.required' => 'Nomor telepon wajib diisi',
            'no_telp.digits_between' => 'Nomor telepon harus 10 sampai 15 digit'
        ]);

        $dpl = Dpl::create($request->all());

        // 🔥 LOG ACTIVITY
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => 'Tambah DPL - ' . $dpl->nama
        ]);

        return redirect('/dpl')->with('success', 'Data DPL berhasil ditambahkan');
    }

    public function edit($nik)
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $data = Dpl::where('nik', $nik)
            ->where('id_periode', $periode_id)
            ->firstOrFail();

        $dplList = Dpl::select('nama')->distinct()->get();

        return view('dpl.edit', compact('data', 'dplList'));
    }

    public function update(Request $request, $nik)
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->merge([
            'nidn' => preg_replace('/[^0-9]/', '', $request->nidn) ?: null,
            'no_telp' => preg_replace('/[^0-9]/', '', $request->no_telp),
        ]);

        $data = Dpl::where('nik', $nik)
            ->where('id_periode', $periode_id)
            ->firstOrFail();

        $request->validate([
            'nik' => [
                'required',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
                    ->ignore($data->nik, 'nik')
            ],
            'nama' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
                    ->ignore($data->nik, 'nik')
            ],
            'no_telp' => 'required|digits_between:10,15',
            'prodi' => 'required|string|min:2|max:100',
            'fakultas' => 'required|string|min:2|max:100',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah digunakan pada periode ini',

            'nama.required' => 'Nama wajib diisi',

            'prodi.required' => 'Program studi wajib diisi',
            'prodi.min' => 'Program studi minimal 2 karakter',
            'prodi.max' => 'Program studi maksimal 100 karakter',

            'fakultas.required' => 'Fakultas wajib diisi',
            'fakultas.min' => 'Fakultas minimal 2 karakter',
            'fakultas.max' => 'Fakultas maksimal 100 karakter',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus mengandung @',
            'email.unique' => 'Email sudah digunakan pada periode ini',

            'no_telp.required' => 'Nomor telepon wajib diisi',
            'no_telp.digits_between' => 'Nomor telepon harus 10 sampai 15 digit'
        ]);

        try {
            $data->update([
                'nik' => $request->nik,
                'nidn' => $request->nidn,
                'nama' => $request->nama,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'prodi' => $request->prodi,
                'fakultas' => $request->fakultas,
            ]);

            // 🔥 LOG ACTIVITY
            LogActivity::create([
                'username' => session('user')->username ?? 'Admin',
                'aktivitas' => 'Edit DPL - ' . $request->nama
            ]);

            return redirect('/dpl')->with('success', 'Data DPL berhasil diupdate');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update data'])->withInput();
        }
    }

    public function delete($nik)
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $dpl = Dpl::where('nik', $nik)
            ->where('id_periode', $periode_id)
            ->firstOrFail();

        $nama_dpl = $dpl->nama;
        $dpl->delete();

        // 🔥 LOG ACTIVITY
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => 'Hapus DPL - ' . $nama_dpl
        ]);

        return redirect('/dpl')->with('success', 'Data DPL berhasil dihapus');
    }

    public function hasilView()
    {
        $this->setPeriodeSession();

        $user = session('user');
        $periode_id = $this->getPeriodeId();

        // ======================================
        // CEK STATUS PUBLISH
        // ======================================

        $status_publish = \App\Models\Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        // JIKA BELUM DIPUBLISH
        if ($status_publish == 0) {

            return view('dpl.belum_publish');

        }

        // ======================================
        // AMBIL USERNAME LOGIN
        // ======================================

        $username = trim((string) $user->username);

        // ======================================
        // CEK APAKAH LOGIN PAKAI EMAIL ATAU NIK
        // ======================================

        $dpl = \App\Models\Dpl::where(function ($query) use ($username) {
            $query->where('nik', $username)
                ->orWhere('email', $username);
        })
            ->where('id_periode', $periode_id)
            ->first();

        // ======================================
        // JIKA DATA DPL TIDAK DITEMUKAN
        // ======================================

        if (!$dpl) {

            return view('dpl.hasil_dpl_view', [
                'kelompok' => collect(),
                'dpl_nama' => 'DPL'
            ]);
        }

        // ======================================
        // AMBIL NIK DPL
        // ======================================

        $nik = trim((string) $dpl->nik);

        // ======================================
        // AMBIL DATA KELOMPOK
        // ======================================

        $kelompok = \App\Models\Kelompok::with(['dpl', 'apl', 'peserta'])
            ->where('nik', $nik)
            ->where('id_periode', $periode_id)
            ->get();

        // ======================================
        // KIRIM KE VIEW
        // ======================================

        return view('dpl.hasil_dpl_view', [
            'kelompok' => $kelompok,
            'dpl_nama' => $dpl->nama
        ]);
    }

    public function detailView($id)
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $kelompok = \App\Models\Kelompok::with(['peserta', 'apl'])
            ->where('id_periode', $periode_id)
            ->where('id_kelompok', $id)
            ->firstOrFail();

        return view('dpl.detail_dpl_view', compact('kelompok'));
    }
}

