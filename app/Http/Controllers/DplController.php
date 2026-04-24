<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Kelompok;
use App\Models\Dpl;

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
            ?? \App\Models\Periode::where('status_publish', 1)->value('id_periode');
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
        return view('dpl.create');
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
            'nidn' => preg_replace('/[^0-9]/', '', $request->nidn),
            'no_telp' => preg_replace('/[^0-9]/', '', $request->no_telp),
            'id_periode' => $periode_id
        ]);

        $request->validate([
            'nik' => [
                'required',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id)),
            ],
            'nidn' => 'required|digits:10',
            'nama' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id)),
            ],
            'no_telp' => 'required|digits_between:10,15'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus mengandung @'
        ]);

        Dpl::create($request->all());

        return redirect('/dpl')->with('success', 'Data DPL berhasil ditambahkan');
    }

    public function edit($nik)
    {
        $periode_id = $this->getPeriodeId();
        $data = Dpl::where('nik', $nik)
            ->where('id_periode', $periode_id)
            ->firstOrFail();

        return view('dpl.edit', compact('data'));
    }

    public function update(Request $request, $nik)
    {
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->merge([
            'nik' => preg_replace('/[^0-9]/', '', $request->nik),
            'nidn' => preg_replace('/[^0-9]/', '', $request->nidn),
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
                    ->ignore($data->id_dpl, 'id_dpl')
            ],
            'nama' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('dpl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
                    ->ignore($data->id_dpl, 'id_dpl')
            ],
            'no_telp' => 'required|digits_between:10,15'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus mengandung @'
        ]);

        try {
            $data->update($request->all());

            return redirect('/dpl')->with('success', 'Data DPL berhasil diupdate');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update data'])->withInput();
        }
    }
    public function delete($nik)
    {
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        Dpl::where('nik', $nik)
            ->where('id_periode', $periode_id)
            ->firstOrFail()
            ->delete()
        ;
        return redirect('/dpl')->with('success', 'Data DPL berhasil dihapus');
    }

    public function hasilView()
    {
        $this->setPeriodeSession();

        $user = session('user');
        $periode_id = $this->getPeriodeId();

        $kelompok = \App\Models\Kelompok::where('nik', $user->username)
            ->where('id_periode', $periode_id)
            ->get();

        return view('dpl.hasil_dpl_view', compact('kelompok'));
    }

    public function detailView($id)
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $kelompok = \App\Models\Kelompok::with(['peserta', 'apl'])
            ->where('id_periode', $periode_id)
            ->findOrFail($id);

        return view('dpl.detail_dpl_view', compact('kelompok'));
    }
}

