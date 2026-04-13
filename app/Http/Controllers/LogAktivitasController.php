<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        $query = LogActivity::query();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $logs = $query->latest()->get();

        return view('log.index', compact('logs'));
    }

    private function logAktivitas($aksi, $deskripsi = null)
    {
        LogActivity::create([
            'username' => session('user')->username ?? 'Admin',
            'aktivitas' => $deskripsi
                ? $aksi . ' - ' . $deskripsi
                : $aksi
        ]);
    }
}
