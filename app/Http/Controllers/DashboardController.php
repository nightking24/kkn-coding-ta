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
    private function setPeriodeSession()
    {
        if (request('periode_id')) {
            session(['periode_id' => request('periode_id')]);
        }
    }

    public function index(Request $request)
    {
        if ($request->periode_id) {
            session(['periode_id' => $request->periode_id]);
        }


        $periode_id = session('periode_id')
            ?? $request->periode_id
            ?? \App\Models\Periode::where('status_publish', 1)->value('id_periode');

        if (!$periode_id) {
            $periode_id = \App\Models\Periode::latest()->value('id_periode');
            session(['periode_id' => $periode_id]);
        }

        $periode = \App\Models\Periode::find($periode_id);

        if (!$periode) {
            session()->forget('periode_id');

            $periode_id = \App\Models\Periode::where('status_publish', 1)->value('id_periode');

            if (!$periode_id) {
                $periode_id = \App\Models\Periode::latest()->value('id_periode');
            }

            if ($periode_id) {
                session(['periode_id' => $periode_id]);
                $periode = \App\Models\Periode::find($periode_id);
            }
        }

        $peserta = 0;
        $kelompok = 0;
        $dpl = 0;
        $apl = 0;

        if ($periode) {

            $peserta = \App\Models\Peserta::whereNotNull('id_kelompok')
                ->whereHas('kelompok', function ($q) use ($periode_id) {
                    $q->where('id_periode', $periode_id);
                })->count();

            $kelompok = \App\Models\Kelompok::where('id_periode', $periode_id)->count();

            $dpl = \App\Models\Kelompok::whereNotNull('nik')
                ->where('id_periode', $periode_id)
                ->distinct()->count('nik');

            $apl = \App\Models\Kelompok::whereNotNull('nim')
                ->where('id_periode', $periode_id)
                ->distinct()->count('nim');
        }

        return view('dashboard_admin', compact(
            'periode',
            'peserta',
            'kelompok',
            'dpl',
            'apl'
        ));
    }

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
    public function exportPDFPeriode($id)
    {
        $this->logAktivitas('Export PDF dari Halaman Dashboard');

        $periode = Periode::findOrFail($id);

        $kelompok = Kelompok::with(['peserta', 'dpl', 'apl'])
            ->where('id_periode', $id)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'kelompok.export_pdf_periode',
            compact('periode', 'kelompok')
        )->setPaper('a4', 'landscape');

        return $pdf->download('laporan_kkn.pdf');
    }

    public function exportExcelPeriode($id)
    {
        $this->logAktivitas('Export Excel dari Halaman Dashboard');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PesertaExport($id),
            'laporan_kkn.xlsx'
        );
    }

    private function logAktivitas($aksi)
    {
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => $aksi
        ]);
    }

}
