<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Kelompok;
use App\Models\Periode;
use App\Models\LogActivity;

class PesertaController extends Controller
{
    // Mengambil ID periode yang sedang aktif dari session, request, atau database
    private function getPeriodeId()
    {
        return session('periode_id')
            ?? request('periode_id')
            ?? Periode::where('status', 'aktif')
                ->value('id_periode');
    }

    // Memeriksa apakah hasil pembagian kelompok sudah dipublish sehingga data tidak dapat diubah
    private function checkPublishLock($periode_id)
    {
        $status = \App\Models\Periode::where('id_periode', $periode_id)
            ->value('status_publish');

        if ($status == 1) {
            return back()->with('error', 'Periode sudah dipublish, data tidak bisa diubah!');
        }

        return null;
    }

    // Menyimpan periode yang dipilih ke dalam session agar tetap digunakan pada halaman berikutnya
    private function setPeriodeSession()
    {
        if (request('periode_id')) {
            session(['periode_id' => request('periode_id')]);
        }
    }

    // Menampilkan hasil pembagian kelompok KKN untuk peserta yang sedang login
    public function hasil()
    {
        try {

            $this->setPeriodeSession();

            $user = session('user');

            // ==============================
            // VALIDASI LOGIN
            // ==============================

            if (!$user || !isset($user->username)) {

                return redirect('/login')
                    ->with('error', 'Session login tidak ditemukan');
            }

            // ==============================
            // AMBIL PERIODE
            // SAMA SEPERTI DPL & APL
            // ==============================

            $periode_id = $this->getPeriodeId();

            if (!$periode_id) {

                return view('peserta.belum_publish');
            }

            // ==============================
            // CEK STATUS PUBLISH
            // ==============================

            $status_publish = \App\Models\Periode::where('id_periode', $periode_id)
                ->value('status_publish');

            // JIKA BELUM DIPUBLISH
            if ($status_publish == 0) {

                return view('peserta.belum_publish');
            }

            // ==============================
            // AMBIL NIM LOGIN
            // ==============================

            $nim = trim((string) $user->username);

            // ==============================
            // CARI PESERTA
            // ==============================

            $peserta = Peserta::with([
                'kelompok.periode',
                'kelompok.dpl',
                'kelompok.apl',
                'kelompok.peserta',
                'kelompok.tuanRumah'
            ])
                ->where('nim', $nim)
                ->where('id_periode', $periode_id)
                ->first();

            // ==============================
            // JIKA TIDAK DITEMUKAN
            // ==============================

            if (!$peserta) {

                return response()->json([
                    'error' => 'Peserta tidak ditemukan',
                    'nim_login' => $nim,
                    'periode_id' => $periode_id
                ]);
            }

            // ==============================
            // BELUM DAPAT KELOMPOK
            // ==============================

            if (!$peserta->id_kelompok) {

                return view('peserta.belum_publish');
            }

            // ==============================
            // TAMPILKAN VIEW
            // ==============================

            return view('peserta.hasil_peserta', compact('peserta'));

        } catch (\Throwable $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }

    // Menempatkan peserta yang belum memiliki kelompok ke kelompok tertentu
    public function tempatkan(Request $request)
    {
        // Mencatat aktivitas admin ke log sistem
        $this->logAktivitas(
            'Tempatkan Peserta',
            "NIM {$request->nim} ditempatkan ke kelompok {$request->id_kelompok}"
        );

        // Mengambil periode yang sedang aktif dari session/request
        $periode_id = $this->getPeriodeId();

        // Mencegah perubahan data jika hasil sudah dipublish
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // Validasi input NIM peserta dan kelompok tujuan
        $request->validate([
            'nim' => 'required',
            'id_kelompok' => 'required'
        ]);

        // Mencari data peserta berdasarkan NIM dan periode aktif
        $peserta = Peserta::where('nim', $request->nim)
            ->where('id_periode', $periode_id)
            ->first();

        // Jika peserta tidak ditemukan
        if (!$peserta) {
            return back()->with('error', 'Peserta tidak ditemukan');
        }

        // Jika peserta sudah memiliki kelompok
        if ($peserta->id_kelompok) {
            return back()->with('error', 'Peserta sudah memiliki kelompok');
        }

        // Mencari kelompok tujuan
        $kelompok = Kelompok::where('id_kelompok', $request->id_kelompok)
            ->where('id_periode', $periode_id)
            ->first();

        // Jika kelompok tidak ditemukan
        if (!$kelompok) {
            return back()->with('error', 'Kelompok tidak ditemukan');
        }

        // Menghitung jumlah anggota kelompok saat ini
        $jumlah = Peserta::where('id_kelompok', $kelompok->id_kelompok)->count();

        // Memastikan kapasitas kelompok belum penuh
        if ($jumlah >= $kelompok->kapasitas) {
            return back()->with('error', 'Kelompok sudah penuh');
        }

        // Menyimpan ID kelompok ke data peserta
        $peserta->update([
            'id_kelompok' => $request->id_kelompok
        ]);

        // Menampilkan pesan sukses
        return back()->with('success', 'Peserta berhasil ditempatkan');
    }

    // Memindahkan peserta dari kelompok asal ke kelompok tujuan yang dipilih admin
    public function pindah(Request $request)
    {
        // Mengambil periode aktif dari session atau request
        $periode_id = $this->getPeriodeId();

        // Mencegah perubahan data jika hasil pembagian sudah dipublish
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // Validasi input NIM peserta dan kelompok tujuan
        $request->validate([
            'nim' => 'required',
            'id_kelompok' => 'required'
        ]);

        // Mencari data peserta berdasarkan NIM dan periode aktif
        $peserta = Peserta::where('nim', $request->nim)
            ->where('id_periode', $periode_id)
            ->first();

        // Jika peserta tidak ditemukan
        if (!$peserta) {
            return back()->with('error', 'Peserta tidak ditemukan');
        }

        // Mencegah pemindahan ke kelompok yang sama
        if ($peserta->id_kelompok == $request->id_kelompok) {
            return back()->with('error', 'Peserta sudah berada di kelompok tersebut');
        }

        // Mencari data kelompok tujuan
        $kelompok = Kelompok::where('id_kelompok', $request->id_kelompok)
            ->where('id_periode', $periode_id)
            ->first();

        // Jika kelompok tujuan tidak ditemukan
        if (!$kelompok) {
            return back()->with('error', 'Kelompok tidak ditemukan');
        }

        // Menghitung jumlah anggota pada kelompok tujuan
        $jumlah = Peserta::where('id_kelompok', $kelompok->id_kelompok)->count();

        // Memastikan kapasitas kelompok tujuan belum penuh
        if ($jumlah >= $kelompok->kapasitas) {
            return back()->with('error', 'Kelompok sudah penuh');
        }

        // Menyimpan nomor kelompok asal peserta untuk kebutuhan log aktivitas
        $kelompok_asal_num = optional(Kelompok::find($peserta->id_kelompok))->nomor_kelompok ?? '-';
        // Menyimpan nomor kelompok tujuan peserta
        $kelompok_tujuan_num = $kelompok->nomor_kelompok;

        // Mencatat aktivitas pemindahan peserta ke tabel log aktivitas
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => "Pindah Peserta - {$peserta->nim} ({$peserta->nama}) dari Kelompok {$kelompok_asal_num} ke Kelompok {$kelompok_tujuan_num}"
        ]);

        // Memperbarui data peserta ke kelompok tujuan
        $peserta->update([
            'id_kelompok' => $request->id_kelompok
        ]);

        // Menampilkan pesan sukses setelah peserta berhasil dipindahkan
        return back()->with('success', 'Peserta berhasil dipindahkan');
    }

    // Menukar kelompok antara dua peserta yang sudah memiliki kelompok
    public function tukar(Request $request)
    {
        // Mengambil periode aktif
        $periode_id = $this->getPeriodeId();

        // Mencegah perubahan jika data sudah dipublish
        if ($lock = $this->checkPublishLock($periode_id)) {
            return $lock;
        }

        // Validasi input dua peserta yang akan ditukar
        $request->validate([
            'nim1' => 'required',
            'nim2' => 'required'
        ]);

        // Mencegah peserta ditukar dengan dirinya sendiri
        if ($request->nim1 == $request->nim2) {
            return back()->with('error', 'Tidak bisa menukar peserta yang sama');
        }

        // Mengambil data peserta pertama
        $p1 = Peserta::where('nim', $request->nim1)
            ->where('id_periode', $periode_id)
            ->first();

        // Mengambil data peserta kedua
        $p2 = Peserta::where('nim', $request->nim2)
            ->where('id_periode', $periode_id)
            ->first();

        // Memastikan kedua peserta ditemukan
        if (!$p1 || !$p2) {
            return back()->with('error', 'Peserta tidak ditemukan');
        }

        // Memastikan kedua peserta sudah memiliki kelompok
        if (!$p1->id_kelompok || !$p2->id_kelompok) {
            return back()->with('error', 'Peserta harus sudah memiliki kelompok');
        }

        // Mencegah pertukaran dalam kelompok yang sama
        if ($p1->id_kelompok == $p2->id_kelompok) {
            return back()->with('error', 'Tidak bisa tukar dalam kelompok yang sama');
        }

        // Menyimpan kelompok asal masing-masing peserta
        $kelompok1 = $p1->id_kelompok;
        $kelompok2 = $p2->id_kelompok;

        // Mencatat aktivitas pertukaran ke log sistem
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => "Tukar Peserta - Menukar {$p1->nim} ({$p1->nama}) [K{$kelompok1}] dengan {$p2->nim} ({$p2->nama}) [K{$kelompok2}]"
        ]);

        // Menukar kelompok peserta pertama
        $p1->update(['id_kelompok' => $kelompok2]);
        // Menukar kelompok peserta kedua
        $p2->update(['id_kelompok' => $kelompok1]);

        // Menampilkan pesan sukses
        return back()->with('success', 'Peserta berhasil ditukar');
    }

    // Menampilkan halaman pemindahan peserta beserta daftar peserta dan kelompok yang tersedia
    public function halamanPindah(Request $request)
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

    // Menampilkan halaman pertukaran peserta beserta daftar peserta dan kelompok yang tersedia
    public function halamanTukar(Request $request)
    {
        $this->setPeriodeSession();

        $periode_id = $this->getPeriodeId();

        $kelompok = Kelompok::where('id_periode', $periode_id)->get();

        $peserta = Peserta::with('kelompok')
            ->whereNotNull('id_kelompok')
            ->where('id_periode', $periode_id)
            ->get();

        return view('peserta.tukar', compact('peserta', 'kelompok'));
    }

    // Menyimpan aktivitas pengguna ke dalam tabel log aktivitas sistem
    private function logAktivitas($aksi, $deskripsi = null)
    {
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => $aksi,
            'deskripsi' => $deskripsi
        ]);
    }
}