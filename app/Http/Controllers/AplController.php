<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Kelompok;
use App\Models\Apl;

class AplController extends Controller
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

        $data = Apl::where('id_periode', $periode_id)->get();

        return view('apl.index', compact('data'));
    }

    public function create()
    {
        return view('apl.create');
    }

    public function store(Request $request)
    {
        $this->setPeriodeSession();
        $periode_id = $this->getPeriodeId();
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->merge([
            'nim' => preg_replace('/[^0-9]/', '', $request->nim),
            'no_telp' => preg_replace('/[^0-9]/', '', $request->no_telp),
            'id_periode' => $periode_id
        ]);

        $request->validate([
            'nim' => [
                'required',
                'digits_between:8,15',
                Rule::unique('apl')->where(function ($q) use ($periode_id) {
                    return $q->where('id_periode', $periode_id);
                }),
            ],
            'nama' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('apl')
                    ->where(function ($q) use ($periode_id) {
                        return $q->where('id_periode', $periode_id);
                    })
            ],
            'no_telp' => 'required|digits_between:10,15'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus mengandung @'
        ]);
        Apl::create($request->all());

        return redirect('/apl')->with('success', 'Data APL berhasil ditambahkan');
    }

    public function edit($nim)
    {
        $periode_id = $this->getPeriodeId();
        $data = Apl::where('nim', $nim)
            ->where('id_periode', $periode_id)
            ->firstOrFail();
        return view('apl.edit', compact('data'));
    }

    public function update(Request $request, $nim)
    {
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->merge([
            'nim' => preg_replace('/[^0-9]/', '', $request->nim),
            'no_telp' => preg_replace('/[^0-9]/', '', $request->no_telp),
        ]);

        $data = Apl::where('nim', $nim)
            ->where('id_periode', $periode_id)
            ->firstOrFail();

        $request->validate([
            'nim' => [
                'required',
                'digits_between:8,15',
                Rule::unique('apl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
                    ->ignore($data->id_apl, 'id_apl')
            ],
            'nama' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('apl')
                    ->where(fn($q) => $q->where('id_periode', $periode_id))
                    ->ignore($data->id_apl, 'id_apl')
            ],
            'no_telp' => 'required|digits_between:10,15'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus mengandung @'
        ]);

        try {
            $data->update($request->all());

            return redirect('/apl')->with('success', 'Data APL berhasil diupdate');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update data'])->withInput();
        }
    }
    public function delete($nim)
    {
        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        Apl::where('nim', $nim)
            ->where('id_periode', $periode_id)
            ->firstOrFail()
            ->delete();
        return redirect('/apl')->with('success', 'Data APL berhasil dihapus');
    }

    public function hasilNew()
    {
        $this->setPeriodeSession();

        $user = session('user');
        $periode_id = $this->getPeriodeId();

        $kelompok = \App\Models\Kelompok::where('nim', $user->username)
            ->where('id_periode', $periode_id)
            ->get();

        return view('apl.hasil_new', compact('kelompok'));
    }

    public function detailNew($id)
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $kelompok = \App\Models\Kelompok::with('peserta', 'dpl')
            ->where('id_periode', $periode_id)
            ->findOrFail($id);

        return view('apl.detail_new', compact('kelompok'));
    }
}