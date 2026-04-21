<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\DplController;
use App\Http\Controllers\AplController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LogAktivitasController;

Route::get('/', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::middleware(['ceklogin', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/periode', [PeriodeController::class, 'index']);
    Route::get('/periode/create', [PeriodeController::class, 'create']);
    Route::post('/periode/store', [PeriodeController::class, 'store']);
    Route::get('/periode/edit/{id}', [PeriodeController::class, 'edit']);
    Route::post('/periode/update/{id}', [PeriodeController::class, 'update']);
    Route::get('/periode/delete/{id}', [PeriodeController::class, 'destroy']);
    Route::get('/periode/{id}', [DashboardController::class, 'detail']);
    Route::get('/periode/{id}/export-pdf', [DashboardController::class, 'exportPDFPeriode']);
    Route::get('/periode/{id}/export-excel', [DashboardController::class, 'exportExcelPeriode']);

    Route::get('/kelompok', [KelompokController::class, 'index']);
    Route::get('/kelompok/create', [KelompokController::class, 'create']);
    Route::post('/kelompok/store', [KelompokController::class, 'store']);
    Route::get('/kelompok/edit/{id}', [KelompokController::class, 'edit']);
    Route::post('/kelompok/update/{id}', [KelompokController::class, 'update']);
    Route::get('/kelompok/delete/{id}', [KelompokController::class, 'delete']);

    Route::get('/dpl', [DplController::class, 'index']);
    Route::get('/dpl/create', [DplController::class, 'create']);
    Route::post('/dpl/store', [DplController::class, 'store']);
    Route::get('/dpl/edit/{nik}', [DplController::class, 'edit']);
    Route::post('/dpl/update/{nik}', [DplController::class, 'update']);
    Route::get('/dpl/delete/{nik}', [DplController::class, 'delete']);

    Route::get('/apl', [AplController::class, 'index']);
    Route::get('/apl/create', [AplController::class, 'create']);
    Route::post('/apl/store', [AplController::class, 'store']);
    Route::get('/apl/edit/{nim}', [AplController::class, 'edit']);
    Route::post('/apl/update/{nim}', [AplController::class, 'update']);
    Route::get('/apl/delete/{nim}', [AplController::class, 'delete']);

    Route::get('/import', [ImportController::class, 'index']);
    Route::post('/import/preview', [ImportController::class, 'preview']);
    Route::post('/import', [ImportController::class, 'store']);

    Route::get('/randomisasi', [KelompokController::class, 'randomisasi']);
    Route::post('/simpan-hasil', [KelompokController::class, 'simpanHasil']);

    Route::get('/hasil-pembagian', [KelompokController::class, 'hasilPembagian'])
        ->name('hasil.pembagian');
    Route::post('/assign-dpl', [KelompokController::class, 'assignDpl'])->name('assign.dpl');
    Route::post('/assign-apl', [KelompokController::class, 'assignApl'])->name('assign.apl');
    Route::post('/tempatkan', [PesertaController::class, 'tempatkan'])->name('peserta.tempatkan');
    Route::post('/reset-pembagian', [KelompokController::class, 'resetPembagian'])->name('reset.pembagian');
    Route::post('/reset-total', [KelompokController::class, 'resetTotal'])->name('reset.total');
    Route::get('/pindah-peserta', [PesertaController::class, 'halamanPindah'])->name('halaman.pindah');
    Route::get('/tukar-peserta', [PesertaController::class, 'halamanTukar'])->name('halaman.tukar');
    Route::post('/pindah-peserta', [PesertaController::class, 'pindah'])->name('peserta.pindah');
    Route::post('/tukar-peserta', [PesertaController::class, 'tukar'])->name('peserta.tukar');
    Route::get('/export-excel/{periode_id}', [KelompokController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf/{periode_id}', [KelompokController::class, 'exportPDF'])->name('export.pdf');

    Route::post('/generate', [KelompokController::class, 'generate']);
    Route::post('/publish', [KelompokController::class, 'publish'])->name('kelompok.publish');

    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);

});

Route::middleware(['ceklogin', 'role:peserta'])->group(function () {
    Route::get('/hasil-peserta', [PesertaController::class, 'hasil']);
});

Route::middleware(['ceklogin', 'role:dpl'])->group(function () {
    Route::get('/dpl-view', [DplController::class, 'hasilView']);
    Route::get('/dpl-view/detail/{id}', [DplController::class, 'detailView']);

});

Route::middleware(['ceklogin', 'role:apl'])->group(function () {
    Route::get('/hasil-apl-new', [AplController::class, 'hasilNew']);
    Route::get('/hasil-apl-new/detail/{id}', [AplController::class, 'detailNew']);

});

