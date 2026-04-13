<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Kelompok;
use App\Models\LogActivity;
class PesertaController extends Controller
{
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

    private function setPeriodeSession()
    {
        if (request('periode_id')) {
            session(['periode_id' => request('periode_id')]);
        }
    }

    public function hasil()
    {
        $user = session('user');

        $periode_id = $this->getPeriodeId();
        $peserta = Peserta::with('kelompok.dpl', 'kelompok.apl', 'kelompok.peserta')
            ->where('nim', $user->username)
            ->where('id_periode', $periode_id)
            ->first();

        return view('peserta.hasil', compact('peserta'));
    }

    public function tempatkan(Request $request)
    {
        $this->logAktivitas(
            'Tempatkan Peserta',
            "NIM {$request->nim} ditempatkan ke kelompok {$request->id_kelompok}"
        );

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->validate([
            'nim' => 'required',
            'id_kelompok' => 'required'
        ]);

        $peserta = Peserta::where('nim', $request->nim)
            ->where('id_periode', $periode_id)
            ->first();

        if (!$peserta) {
            return back()->with('error', 'Peserta tidak ditemukan');
        }

        if ($peserta->id_kelompok) {
            return back()->with('error', 'Peserta sudah memiliki kelompok');
        }

        $kelompok = Kelompok::where('id_kelompok', $request->id_kelompok)
            ->where('id_periode', $periode_id)
            ->first();

        if (!$kelompok) {
            return back()->with('error', 'Kelompok tidak ditemukan');
        }

        $jumlah = Peserta::where('id_kelompok', $kelompok->id_kelompok)->count();

        if ($jumlah >= $kelompok->kapasitas) {
            return back()->with('error', 'Kelompok sudah penuh');
        }

        $peserta->update([
            'id_kelompok' => $request->id_kelompok
        ]);

        return back()->with('success', 'Peserta berhasil ditempatkan');
    }

    public function pindah(Request $request)
    {
        $this->logAktivitas(
            'Pindah Peserta',
            "NIM {$request->nim} dipindah ke kelompok {$request->id_kelompok}"
        );

        $periode_id = $this->getPeriodeId();

        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->validate([
            'nim' => 'required',
            'id_kelompok' => 'required'
        ]);

        $peserta = Peserta::where('nim', $request->nim)
            ->where('id_periode', $periode_id)
            ->first();

        if (!$peserta) {
            return back()->with('error', 'Peserta tidak ditemukan');
        }

        if ($peserta->id_kelompok == $request->id_kelompok) {
            return back()->with('error', 'Peserta sudah berada di kelompok tersebut');
        }

        $kelompok = Kelompok::where('id_kelompok', $request->id_kelompok)
            ->where('id_periode', $periode_id)
            ->first();

        if (!$kelompok) {
            return back()->with('error', 'Kelompok tidak ditemukan');
        }

        $jumlah = Peserta::where('id_kelompok', $kelompok->id_kelompok)->count();

        if ($jumlah >= $kelompok->kapasitas) {
            return back()->with('error', 'Kelompok sudah penuh');
        }

        $peserta->update([
            'id_kelompok' => $request->id_kelompok
        ]);

        return back()->with('success', 'Peserta berhasil dipindahkan');
    }

    public function tukar(Request $request)
    {
        $this->logAktivitas(
            'Tukar Peserta',
            "Menukar {$request->nim1} dengan {$request->nim2}"
        );

        $periode_id = $this->getPeriodeId();
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        $request->validate([
            'nim1' => 'required',
            'nim2' => 'required'
        ]);

        if ($request->nim1 == $request->nim2) {
            return back()->with('error', 'Tidak bisa menukar peserta yang sama');
        }

        $p1 = Peserta::where('nim', $request->nim1)
            ->where('id_periode', $periode_id)
            ->first();

        $p2 = Peserta::where('nim', $request->nim2)
            ->where('id_periode', $periode_id)
            ->first();

        if (!$p1 || !$p2) {
            return back()->with('error', 'Peserta tidak ditemukan');
        }

        if (!$p1->id_kelompok || !$p2->id_kelompok) {
            return back()->with('error', 'Peserta harus sudah memiliki kelompok');
        }

        $kelompok1 = $p1->id_kelompok;
        $kelompok2 = $p2->id_kelompok;

        $p1->update(['id_kelompok' => $kelompok2]);
        $p2->update(['id_kelompok' => $kelompok1]);

        return back()->with('success', 'Peserta berhasil ditukar');
    }

    public function halamanPindah()
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $peserta = Peserta::with('kelompok')
            ->whereNotNull('id_kelompok')
            ->where('id_periode', $periode_id)
            ->get();

        $kelompok = Kelompok::where('id_periode', $periode_id)->get();

        return view('peserta.pindah', compact('peserta', 'kelompok'));
    }

    public function halamanTukar()
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $peserta = Peserta::with('kelompok')
            ->whereNotNull('id_kelompok')
            ->where('id_periode', $periode_id)
            ->get();

        return view('peserta.tukar', compact('peserta'));
    }

    private function logAktivitas($aksi, $deskripsi = null)
    {
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => $aksi,
            'deskripsi' => $deskripsi
        ]);
    }
}