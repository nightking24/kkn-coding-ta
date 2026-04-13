<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        $periode = Periode::latest()->get();
        return view('periode.index', compact('periode'));
    }

    public function create()
    {
        return view('periode.create');
    }

    public function store(Request $request)
    {
        // 🔥 VALIDASI
        $request->validate([
            'nama_kkn' => 'required',
            'tahun_kkn' => 'required|numeric',
            'lokasi' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'status' => 'required'
        ]);

        try {
            // 🔥 SIMPAN DATA
            $periode = Periode::create([
                'nama_kkn' => $request->nama_kkn,
                'tahun_kkn' => $request->tahun_kkn,
                'lokasi' => $request->lokasi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
                'status_publish' => 0 // default belum publish
            ]);

            // 🔥 SET SESSION PERIODE AKTIF (PENTING)
            session(['periode_id' => $periode->id_periode]);

            return redirect('/periode')->with('success', 'Periode berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $periode = Periode::findOrFail($id);
        return view('periode.edit', compact('periode'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kkn' => 'required',
            'tahun_kkn' => 'required|numeric',
            'lokasi' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'status' => 'required'
        ]);

        $data = Periode::findOrFail($id);

        try {
            $data->update([
                'nama_kkn' => $request->nama_kkn,
                'tahun_kkn' => $request->tahun_kkn,
                'lokasi' => $request->lokasi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status
            ]);

            return redirect('/periode')->with('success', 'Periode berhasil diupdate');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal update data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Periode::destroy($id);
            return redirect('/periode')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus data']);
        }
    }
}