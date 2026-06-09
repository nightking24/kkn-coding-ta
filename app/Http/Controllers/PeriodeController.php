<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Models\LogActivity;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    // Menampilkan daftar seluruh periode KKN yang tersimpan di sistem
    public function index()
    {
        // Mengambil seluruh data periode dan mengurutkan dari yang terbaru
        $periode = Periode::latest()->get();
        // Mengirim data periode ke halaman index
        return view('periode.index', compact('periode'));
    }

    // Menampilkan halaman form tambah periode KKN
    public function create()
    {
        return view('periode.create');
    }

    // Menyimpan data periode KKN baru ke database
    public function store(Request $request)
    {
        // Memastikan semua input wajib diisi sesuai aturan (validasi)
        $request->validate([
            'nama_kkn' => 'required',
            'tahun_kkn' => 'required|numeric',
            'lokasi' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'status' => 'required'
        ]);

        try {
            // SIMPAN DATA
            $periode = Periode::create([
                'nama_kkn' => $request->nama_kkn,
                'tahun_kkn' => $request->tahun_kkn,
                'lokasi' => $request->lokasi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
                'status_publish' => 0 // default belum publish
            ]);

            // SET SESSION PERIODE AKTIF (PENTING)
            session(['periode_id' => $periode->id_periode]);

            // LOG ACTIVITY
            LogActivity::create([
                'username' => session('user')->username ?? 'Admin',
                'aktivitas' => 'Tambah Periode - ' . $request->nama_kkn
            ]);

            return redirect('/periode')->with('success', 'Periode berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Menampilkan halaman edit data periode berdasarkan ID periode
    public function edit($id)
    {
        $periode = Periode::findOrFail($id);
        return view('periode.edit', compact('periode'));
    }

    // Memperbarui data periode yang sudah ada
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

            // JIKA PERIODE INI BERJALAN
            // MAKA SET JADI SESSION AKTIF
            if ($request->status == 'berjalan') {

                session([
                    'periode_id' => $data->id_periode
                ]);
            }

            // LOG ACTIVITY
            LogActivity::create([
                'username' => session('user')->username ?? 'Admin',
                'aktivitas' => 'Edit Periode - ' . $request->nama_kkn
            ]);

            return redirect('/periode')->with('success', 'Periode berhasil diupdate');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal update data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Menghapus data periode berdasarkan ID periode
    public function destroy($id)
    {
        try {
            $periode = Periode::findOrFail($id);
            Periode::destroy($id);

            // LOG ACTIVITY
            LogActivity::create([
                'username' => session('user')->username ?? 'Admin',
                'aktivitas' => 'Hapus Periode - ' . $periode->nama_kkn
            ]);

            return redirect('/periode')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus data']);
        }
    }
}