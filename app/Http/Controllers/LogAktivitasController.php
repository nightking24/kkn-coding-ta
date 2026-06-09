<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity;

class LogAktivitasController extends Controller
{
     // Menampilkan daftar log aktivitas pengguna dan melakukan filter berdasarkan tanggal
    public function index(Request $request)
    {
        // Validasi input tanggal filter
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        // Membuat query awal untuk tabel log aktivitas
        $query = LogActivity::query();

        // Jika tanggal awal dan tanggal akhir diisi
        if ($request->start_date && $request->end_date) {
            // Menampilkan log berdasarkan rentang tanggal yang dipilih
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Mengambil seluruh log dan mengurutkan dari yang terbaru
        $logs = $query->latest()->get();

        // Mengirim data log ke halaman index
        return view('log.index', compact('logs'));
    }

    // Menyimpan aktivitas pengguna ke tabel log aktivitas
    private function logAktivitas($aksi, $deskripsi = null)
    {
        LogActivity::create([
             // Mengambil username pengguna yang sedang login
            'username' => session('user')->username ?? 'Admin',

            // Menyimpan aktivitas beserta deskripsi jika tersedia
            'aktivitas' => $deskripsi
                ? $aksi . ' - ' . $deskripsi
                : $aksi
        ]);
    }
}
