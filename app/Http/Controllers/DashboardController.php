<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Periode;
use App\Models\Peserta;
use App\Models\Kelompok;
use App\Models\Dpl;
use App\Models\Apl;
use App\Models\LogActivity;

class DashboardController extends Controller
{
    // Menyimpan periode yang dipilih ke dalam session
    private function setPeriodeSession()
    {
        if (request('periode_id')) {
            session(['periode_id' => request('periode_id')]);
        }
    }

    // Menampilkan halaman dashboard beserta statistik data periode yang dipilih
    public function index(Request $request)
    {
        // Jika admin memilih periode dari dropdown
        if ($request->periode_id) {

            // Simpan periode yang dipilih ke session
            session(['periode_id' => $request->periode_id]);
        }

        // Menentukan periode aktif dengan urutan prioritas:
        // 1. Session
        // 2. Request
        // 3. Periode yang sudah dipublish
        $periode_id = session('periode_id')
            ?? $request->periode_id
            ?? \App\Models\Periode::where('status_publish', 1)->value('id_periode');

        // Jika belum ada periode aktif
        if (!$periode_id) {
            // Ambil periode terbaru
            $periode_id = \App\Models\Periode::latest()->value('id_periode');

            // Simpan ke session
            session(['periode_id' => $periode_id]);
        }

        // Mengambil data periode berdasarkan ID
        $periode = \App\Models\Periode::find($periode_id);

        // Jika periode tidak ditemukan
        if (!$periode) {
            // Hapus session periode lama
            session()->forget('periode_id');

            // Cari periode yang dipublish
            $periode_id = \App\Models\Periode::where('status_publish', 1)->value('id_periode');

            // Jika tidak ada periode publish
            if (!$periode_id) {
                // Gunakan periode terbaru
                $periode_id = \App\Models\Periode::latest()->value('id_periode');
            }

            // Jika periode berhasil ditemukan
            if ($periode_id) {
                // Simpan ke session
                session(['periode_id' => $periode_id]);
                // Ambil data periode
                $periode = \App\Models\Periode::find($periode_id);
            }
        }

        // Inisialisasi statistik dashboard
        $peserta = 0;
        $kelompok = 0;
        $dpl = 0;
        $apl = 0;

        // Jika periode ditemukan
        if ($periode) {

        // Menghitung jumlah peserta yang sudah mendapatkan kelompok
            $peserta = \App\Models\Peserta::whereNotNull('id_kelompok')
                ->whereHas('kelompok', function ($q) use ($periode_id) {
                    // Hanya menghitung kelompok pada periode aktif
                    $q->where('id_periode', $periode_id);
                })->count();

            // Menghitung jumlah kelompok pada periode aktif
            $kelompok = \App\Models\Kelompok::where('id_periode', $periode_id)->count();

            // Menghitung jumlah DPL unik
            $dpl = \App\Models\Kelompok::whereNotNull('nik')
                ->where('id_periode', $periode_id)
                ->distinct()->count('nik');

            // Menghitung jumlah APL unik
            $apl = \App\Models\Kelompok::whereNotNull('nim')
                ->where('id_periode', $periode_id)
                ->distinct()->count('nim');
        }

        // Mengirim data ke halaman dashboard
        return view('dashboard_admin', compact(
            'periode',
            'peserta',
            'kelompok',
            'dpl',
            'apl'
        ));
    }

    // Menampilkan detail periode beserta kelompok, peserta, DPL, dan APL yang terkait
    public function detail($id)
    {
        $periode = Periode::findOrFail($id);

        $kelompok = Kelompok::with(['peserta', 'dpl', 'apl'])
            ->where('id_periode', $id)
            ->get();

        $total_kelompok = $kelompok->count();

        $total_peserta = $kelompok->sum(function ($k) {
            return $k->peserta->count();
        });

        return view('detail_periode', compact(
            'periode',
            'kelompok',
            'total_kelompok',
            'total_peserta'
        ));
    }

    // Mengekspor laporan hasil pembagian kelompok dalam format PDF
    public function exportPDFPeriode($id)
    {
         // Mencatat aktivitas export PDF ke tabel log aktivitas
        $this->logAktivitas('Export PDF dari Halaman Dashboard');

        // Mengambil data periode berdasarkan ID
        $periode = Periode::findOrFail($id);

        // Mengambil seluruh kelompok beserta relasi peserta, DPL, dan APL
        $kelompok = Kelompok::with(['peserta', 'dpl', 'apl'])
            ->where('id_periode', $id)
            ->get();

        // Membuat file PDF dari view Blade
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'kelompok.export_pdf_periode',
            compact('periode', 'kelompok')

        // Mengatur ukuran kertas A4 landscape
        )->setPaper('a4', 'landscape');

        // Membuat nama file PDF secara otomatis
        $namaFile =
            'laporan_kkn_reguler_' .
            $periode->tahun_kkn .
            '.pdf';

        // Mengunduh file PDF
        return $pdf->download($namaFile);
    }

    // Mengekspor laporan hasil pembagian kelompok dalam format Excel
    public function exportExcelPeriode($id)
    {
        // Mencatat aktivitas export Excel ke log sistem
        $this->logAktivitas('Export Excel dari Halaman Dashboard');

        // Mengambil data periode berdasarkan ID
        $periode = Periode::findOrFail($id);

        // Membuat nama file Excel otomatis
        $namaFile =
            'laporan_kkn_reguler_' .
            $periode->tahun_kkn .
            '.xlsx';

        // Membuat dan mengunduh file Excel
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PesertaExport($id),
            $namaFile
        );
    }

    // Menyimpan aktivitas pengguna ke dalam tabel log aktivitas
    private function logAktivitas($aksi)
    {
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => $aksi
        ]);
    }

}
