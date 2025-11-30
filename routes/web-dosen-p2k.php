<?php

use App\Http\Controllers\DosenP2KController;
use App\Http\Controllers\Mahasiswa\PKSController;
use App\Http\Controllers\SuratPengantarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dosen P2K Routes
|--------------------------------------------------------------------------
|
| Here is where you can register dosen P2K routes for your application.
|
*/

Route::middleware(['role:dosen'])->prefix('dosen/p2k')->name('dosen.p2k.')->group(function () {
    Route::get('/', [DosenP2KController::class, 'index'])->name('index');
    Route::get('/get-kelas', [DosenP2KController::class, 'getKelasByTahunAkademik'])->name('get-kelas');
    Route::get('/kelas/{id}', [DosenP2KController::class, 'detailKelas'])->name('detail-kelas');
    Route::post('/laporan/{id}/validate', [DosenP2KController::class, 'validateLaporanAkhir'])->name('validate-laporan');
});

// Rute untuk dosen mengakses cetak dokumen
Route::middleware(['role:dosen,admin'])->group(function () {
    // Akses ke cetak surat pengantar dan PKS untuk dosen
    Route::get('dosen/surat-pengantar/{id}/cetak', [SuratPengantarController::class, 'generatePdf'])->name('dosen.surat-pengantar.cetak');
    Route::get('dosen/pks/{id}/cetak', [PKSController::class, 'generatePdf'])->name('dosen.pks.cetak');
});