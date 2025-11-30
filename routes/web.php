<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriUmkmController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\SettingSuratPengantarController;
use App\Http\Controllers\Mahasiswa\PKSController;
use App\Http\Controllers\Admin\SuratPengantarController as AdminSuratPengantarController;
use App\Http\Controllers\Admin\PKSController as AdminPKSController;
use App\Http\Controllers\Admin\SertifikatKwuController;
use App\Http\Controllers\Admin\KelasDosenP2KController;
use App\Http\Controllers\Admin\LaporanP2KController;
use App\Http\Controllers\Admin\P2K\MahasiswaController;
use App\Http\Controllers\Admin\P2K\IAController;
use App\Http\Controllers\Mahasiswa\DataUmkmController;
use App\Http\Controllers\Mahasiswa\LaporanAkhirController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\Admin\UjianController as AdminUjianController;
use App\Http\Controllers\Admin\SoalController as AdminSoalController;
use App\Http\Controllers\Admin\PilihanController as AdminPilihanController;
use App\Http\Controllers\Admin\SesiUjianController as AdminSesiUjianController;
use App\Http\Controllers\Admin\JawabanMahasiswaController as AdminJawabanMahasiswaController;
use Illuminate\Support\Facades\Route;

// Maintenance Route (Public Access)
Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
Route::get('/maintenance/status', [MaintenanceController::class, 'status'])->name('maintenance.status');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('session')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('session')->group(function () {
    // Rute umum untuk semua role
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    });

    // Master Routes - Hanya untuk Admin
    Route::prefix('master')->middleware('role:admin')->group(function () {
        // Kategori UMKM Routes - Hanya Admin
        Route::prefix('kategori-umkm')->group(function () {
            Route::get('/', [KategoriUmkmController::class, 'index'])->name('master.kategori-umkm.index');
            Route::get('/data', [KategoriUmkmController::class, 'getData'])->name('master.kategori-umkm.data');
            Route::post('/', [KategoriUmkmController::class, 'store'])->name('master.kategori-umkm.store');
            Route::get('/{id}', [KategoriUmkmController::class, 'show'])->name('master.kategori-umkm.show');
            Route::put('/{id}', [KategoriUmkmController::class, 'update'])->name('master.kategori-umkm.update');
            Route::delete('/{id}', [KategoriUmkmController::class, 'destroy'])->name('master.kategori-umkm.destroy');
        });

        // Tahun Akademik Routes - Hanya Admin
        Route::prefix('tahun-akademik')->group(function () {
            Route::get('/', [TahunAkademikController::class, 'index'])->name('master.tahun-akademik.index');
            Route::get('/data', [TahunAkademikController::class, 'getData'])->name('master.tahun-akademik.data');
            Route::post('/', [TahunAkademikController::class, 'store'])->name('master.tahun-akademik.store');
            Route::get('/{id}', [TahunAkademikController::class, 'show'])->name('master.tahun-akademik.show');
            Route::put('/{id}', [TahunAkademikController::class, 'update'])->name('master.tahun-akademik.update');
            Route::delete('/{id}', [TahunAkademikController::class, 'destroy'])->name('master.tahun-akademik.destroy');
        });

        // Ujian Routes - Hanya untuk Admin
        Route::prefix('ujian')->group(function () {
            Route::get('/', [AdminUjianController::class, 'index'])->name('master.ujian.index');
            Route::get('/create', [AdminUjianController::class, 'create'])->name('master.ujian.create');
            Route::post('/', [AdminUjianController::class, 'store'])->name('master.ujian.store');
            Route::get('/{id}', [AdminUjianController::class, 'show'])->name('master.ujian.show');
            Route::get('/{id}/edit', [AdminUjianController::class, 'edit'])->name('master.ujian.edit');
            Route::put('/{id}', [AdminUjianController::class, 'update'])->name('master.ujian.update');
            Route::delete('/{id}', [AdminUjianController::class, 'destroy'])->name('master.ujian.destroy');

            // Encrypted routes for security
            Route::get('/show/{encryptedId}', [AdminUjianController::class, 'showEncrypted'])->name('master.ujian.show.encrypted');
            Route::get('/edit/{encryptedId}', [AdminUjianController::class, 'editEncrypted'])->name('master.ujian.edit.encrypted');
            Route::put('/update/{encryptedId}', [AdminUjianController::class, 'updateEncrypted'])->name('master.ujian.update.encrypted');
            Route::delete('/delete/{encryptedId}', [AdminUjianController::class, 'destroyEncrypted'])->name('master.ujian.destroy.encrypted');
            Route::get('/soal/{encryptedId}', [AdminSoalController::class, 'indexEncrypted'])->name('master.soal.index.encrypted');
            Route::get('/soal/create/{encryptedId}', [AdminSoalController::class, 'createEncrypted'])->name('master.soal.create.encrypted');
            Route::post('/soal/import/{encryptedId}', [AdminSoalController::class, 'importEncrypted'])->name('master.soal.import.encrypted');
            Route::get('/soal/download-template/{encryptedId}', [AdminSoalController::class, 'downloadTemplateEncrypted'])->name('master.soal.downloadTemplate.encrypted');
            Route::get('/soal/show/{encryptedId}', [AdminSoalController::class, 'showEncrypted'])->name('master.soal.show.encrypted');
            Route::get('/soal/edit/{encryptedId}', [AdminSoalController::class, 'editEncrypted'])->name('master.soal.edit.encrypted');
            Route::put('/soal/update/{encryptedId}', [AdminSoalController::class, 'updateEncrypted'])->name('master.soal.update.encrypted');
            Route::delete('/soal/delete/{encryptedId}', [AdminSoalController::class, 'destroyEncrypted'])->name('master.soal.destroy.encrypted');
            Route::get('/preview/{encryptedId}', [AdminUjianController::class, 'previewEncrypted'])->name('master.ujian.preview.encrypted');
            Route::post('/{id}/toggle-status', [AdminUjianController::class, 'toggleStatus'])->name('master.ujian.toggleStatus');

            // Soal Routes
            Route::get('/{id_ujian}/soal', [AdminSoalController::class, 'index'])->name('master.soal.index');
            Route::get('/{id_ujian}/soal/create', [AdminSoalController::class, 'create'])->name('master.soal.create');
            Route::post('/soal', [AdminSoalController::class, 'store'])->name('master.soal.store');
            Route::get('/soal/{id}', [AdminSoalController::class, 'show'])->name('master.soal.show');
            Route::get('/soal/{id}/edit', [AdminSoalController::class, 'edit'])->name('master.soal.edit');
            Route::put('/soal/{id}', [AdminSoalController::class, 'update'])->name('master.soal.update');
            Route::delete('/soal/{id}', [AdminSoalController::class, 'destroy'])->name('master.soal.destroy');
            Route::post('/{id_ujian}/soal/import', [AdminSoalController::class, 'import'])->name('master.soal.import');
            Route::get('/{id_ujian}/soal/download-template', [AdminSoalController::class, 'downloadTemplate'])->name('master.soal.downloadTemplate');
            Route::post('/{id_ujian}/soal/reorder', [AdminSoalController::class, 'reorderQuestions'])->name('master.soal.reorder');

            // Pilihan Routes
            Route::get('/pilihan/{id_soal}', [AdminPilihanController::class, 'index'])->name('master.pilihan.index');
            Route::get('/pilihan/{id_soal}/create', [AdminPilihanController::class, 'create'])->name('master.pilihan.create');
            Route::post('/pilihan', [AdminPilihanController::class, 'store'])->name('master.pilihan.store');
            Route::get('/pilihan/show/{id}', [AdminPilihanController::class, 'show'])->name('master.pilihan.show');
            Route::get('/pilihan/{id}/edit', [AdminPilihanController::class, 'edit'])->name('master.pilihan.edit');
            Route::put('/pilihan/{id}', [AdminPilihanController::class, 'update'])->name('master.pilihan.update');
            Route::delete('/pilihan/{id}', [AdminPilihanController::class, 'destroy'])->name('master.pilihan.destroy');
            Route::post('/pilihan/{id}/set-correct', [AdminPilihanController::class, 'setCorrectAnswer'])->name('master.pilihan.setCorrect');
            Route::post('/pilihan/{id_soal}/bulk-create', [AdminPilihanController::class, 'bulkCreate'])->name('master.pilihan.bulkCreate');

            // Pilihan Encrypted Routes
            Route::get('/pilihan/by-soal/{encryptedId}', [AdminPilihanController::class, 'indexEncrypted'])->name('master.pilihan.index.encrypted');
            Route::get('/pilihan/create/{encryptedId}', [AdminPilihanController::class, 'createEncrypted'])->name('master.pilihan.create.encrypted');
            Route::get('/pilihan/show/{encryptedId}', [AdminPilihanController::class, 'showEncrypted'])->name('master.pilihan.show.encrypted');
            Route::get('/pilihan/edit/{encryptedId}', [AdminPilihanController::class, 'editEncrypted'])->name('master.pilihan.edit.encrypted');
            Route::put('/pilihan/update/{encryptedId}', [AdminPilihanController::class, 'updateEncrypted'])->name('master.pilihan.update.encrypted');
            Route::delete('/pilihan/delete/{encryptedId}', [AdminPilihanController::class, 'destroyEncrypted'])->name('master.pilihan.destroy.encrypted');
            Route::post('/pilihan/set-correct/{encryptedId}', [AdminPilihanController::class, 'setCorrectAnswerEncrypted'])->name('master.pilihan.setCorrect.encrypted');
            Route::post('/pilihan/bulk-create/{encryptedId}', [AdminPilihanController::class, 'bulkCreateEncrypted'])->name('master.pilihan.bulkCreate.encrypted');
        });

        // Sesi Ujian Routes
        Route::prefix('sesi_ujian')->group(function () {
            Route::get('/', [AdminSesiUjianController::class, 'index'])->name('master.sesi_ujian.index');
            Route::get('/{id_ujian}', [AdminSesiUjianController::class, 'index'])->name('master.sesi_ujian.index_by_ujian');
            Route::get('/by-ujian/{encryptedId}', [AdminSesiUjianController::class, 'indexEncrypted'])->name('master.sesi_ujian.index_by_ujian.encrypted');
            Route::get('/show/{encryptedId}', [AdminSesiUjianController::class, 'showEncrypted'])->name('master.sesi_ujian.show.encrypted');
            Route::get('/jawaban-detail/{encryptedId}', [AdminSesiUjianController::class, 'jawabanDetailEncrypted'])->name('master.sesi_ujian.jawaban_detail.encrypted');
            Route::post('/force-finish/{encryptedId}', [AdminSesiUjianController::class, 'forceFinishEncrypted'])->name('master.sesi_ujian.forceFinish.encrypted');
            Route::post('/extend-time/{encryptedId}', [AdminSesiUjianController::class, 'extendTimeEncrypted'])->name('master.sesi_ujian.extendTime.encrypted');
            Route::post('/reset/{encryptedId}', [AdminSesiUjianController::class, 'resetEncrypted'])->name('master.sesi_ujian.reset.encrypted');
            Route::delete('/delete/{encryptedId}', [AdminSesiUjianController::class, 'deleteEncrypted'])->name('master.sesi_ujian.delete.encrypted');
            Route::get('/statistics/{encryptedId}', [AdminSesiUjianController::class, 'statisticsEncrypted'])->name('master.sesi_ujian.statistics.encrypted');
            Route::get('/export-results/{encryptedId}', [AdminSesiUjianController::class, 'exportResultsEncrypted'])->name('master.sesi_ujian.exportResults.encrypted');

            // Legacy routes for backward compatibility
            Route::get('/show/{id}', [AdminSesiUjianController::class, 'show'])->name('master.sesi_ujian.show');
            Route::get('/jawaban-detail/{id}', [AdminSesiUjianController::class, 'jawabanDetail'])->name('master.sesi_ujian.jawaban_detail');
            Route::post('/{id}/force-finish', [AdminSesiUjianController::class, 'forceFinish'])->name('master.sesi_ujian.forceFinish');
            Route::post('/{id}/extend-time', [AdminSesiUjianController::class, 'extendTime'])->name('master.sesi_ujian.extendTime');
            Route::post('/{id}/reset', [AdminSesiUjianController::class, 'reset'])->name('master.sesi_ujian.reset');
            Route::delete('/{id}', [AdminSesiUjianController::class, 'delete'])->name('master.sesi_ujian.delete');
            Route::get('/{id_ujian}/statistics', [AdminSesiUjianController::class, 'statistics'])->name('master.sesi_ujian.statistics');
            Route::get('/{id_ujian}/export-results', [AdminSesiUjianController::class, 'exportResults'])->name('master.sesi_ujian.exportResults');
        });

        // Jawaban Mahasiswa Routes
        Route::prefix('jawaban_mahasiswa')->group(function () {
            Route::get('/', [AdminJawabanMahasiswaController::class, 'index'])->name('master.jawaban_mahasiswa.index');
            Route::get('/{id_sesi}', [AdminJawabanMahasiswaController::class, 'index'])->name('master.jawaban_mahasiswa.index_session');
            Route::get('/show/{id}', [AdminJawabanMahasiswaController::class, 'show'])->name('master.jawaban_mahasiswa.show');
            Route::get('/{id}/edit', [AdminJawabanMahasiswaController::class, 'edit'])->name('master.jawaban_mahasiswa.edit');
            Route::put('/{id}', [AdminJawabanMahasiswaController::class, 'update'])->name('master.jawaban_mahasiswa.update');
            Route::delete('/{id}', [AdminJawabanMahasiswaController::class, 'destroy'])->name('master.jawaban_mahasiswa.destroy');
            Route::post('/{id_sesi}/bulk-answer', [AdminJawabanMahasiswaController::class, 'bulkAnswer'])->name('master.jawaban_mahasiswa.bulkAnswer');
            Route::get('/{id_sesi}/review', [AdminJawabanMahasiswaController::class, 'reviewMode'])->name('master.jawaban_mahasiswa.review');
            Route::get('/review/{encryptedId}', [AdminJawabanMahasiswaController::class, 'reviewModeEncrypted'])->name('master.jawaban_mahasiswa.review.encrypted');
            Route::get('/statistics/{id_ujian}', [AdminJawabanMahasiswaController::class, 'statistics'])->name('master.jawaban_mahasiswa.statistics');
        });
    });

    Route::prefix('kwu')->middleware('role:admin')->group(function () {
        // Sertifikat KWU Routes - Hanya Admin
        Route::prefix('sertifikat-kwu')->group(function () {
            Route::get('/', [SertifikatKwuController::class, 'index'])->name('kwu.sertifikat-kwu.index');
            Route::get('/data', [SertifikatKwuController::class, 'getData'])->name('kwu.sertifikat-kwu.data');
            Route::post('/', [SertifikatKwuController::class, 'store'])->name('kwu.sertifikat-kwu.store');
            Route::get('/import-template', [SertifikatKwuController::class, 'importTemplate'])->name('kwu.sertifikat-kwu.import-template');
            Route::post('/import', [SertifikatKwuController::class, 'import'])->name('kwu.sertifikat-kwu.import');
            Route::post('/validate-data', [SertifikatKwuController::class, 'validateData'])->name('kwu.sertifikat-kwu.validate-data');
            Route::post('/export-validation-results', [SertifikatKwuController::class, 'exportValidationResults'])->name('kwu.sertifikat-kwu.export-validation-results');
            Route::get('/{id}', [SertifikatKwuController::class, 'show'])->name('kwu.sertifikat-kwu.show');
            Route::put('/{id}', [SertifikatKwuController::class, 'update'])->name('kwu.sertifikat-kwu.update');
            Route::delete('/{id}', [SertifikatKwuController::class, 'destroy'])->name('kwu.sertifikat-kwu.destroy');
        });
    });

    // Settings Routes - Hanya untuk Admin
    Route::prefix('settings')->middleware('role:admin')->group(function () {
        // Setting Surat Pengantar Routes
        Route::prefix('surat-pengantar')->group(function () {
            Route::get('/', [SettingSuratPengantarController::class, 'index'])->name('settings.surat-pengantar.index');
            Route::get('/data', [SettingSuratPengantarController::class, 'getData'])->name('settings.surat-pengantar.data');
            Route::post('/', [SettingSuratPengantarController::class, 'store'])->name('settings.surat-pengantar.store');
            Route::get('/{id}', [SettingSuratPengantarController::class, 'show'])->name('settings.surat-pengantar.show');
            Route::put('/{id}', [SettingSuratPengantarController::class, 'update'])->name('settings.surat-pengantar.update');
            Route::delete('/{id}', [SettingSuratPengantarController::class, 'destroy'])->name('settings.surat-pengantar.destroy');
        });
    });

    // P2K Routes - Hanya untuk Admin
    Route::prefix('p2k')->middleware('role:admin,tamu')->group(function () {
        // Mahasiswa Routes
        Route::prefix('mahasiswa')->group(function () {
            Route::get('/', [MahasiswaController::class, 'index'])->name('p2k.mahasiswa.index');
            Route::get('/data', [MahasiswaController::class, 'getData'])->name('p2k.mahasiswa.data');
            Route::post('/', [MahasiswaController::class, 'store'])->name('p2k.mahasiswa.store');
            Route::get('/import-template', [MahasiswaController::class, 'importTemplate'])->name('p2k.mahasiswa.import-template');
            Route::post('/import', [MahasiswaController::class, 'import'])->name('p2k.mahasiswa.import');
            Route::post('/validate-data', [MahasiswaController::class, 'validateData'])->name('p2k.mahasiswa.validate-data');
            Route::post('/export-unmatched-data', [MahasiswaController::class, 'exportUnmatchedData'])->name('p2k.mahasiswa.export-unmatched-data');
            Route::get('/{nim}', [MahasiswaController::class, 'show'])->name('p2k.mahasiswa.show');
            Route::put('/{nim}', [MahasiswaController::class, 'update'])->name('p2k.mahasiswa.update');
            Route::delete('/{nim}', [MahasiswaController::class, 'destroy'])->name('p2k.mahasiswa.destroy');
        });

        // Surat Pengantar Routes
        Route::prefix('surat-pengantar')->group(function () {
            Route::get('/', [AdminSuratPengantarController::class, 'index'])->name('p2k.surat-pengantar.index');
            Route::get('/data', [AdminSuratPengantarController::class, 'getData'])->name('p2k.surat-pengantar.data');
            Route::post('/', [AdminSuratPengantarController::class, 'store'])->name('p2k.surat-pengantar.store');
            Route::get('/{id}', [AdminSuratPengantarController::class, 'show'])->name('p2k.surat-pengantar.show');
            Route::put('/{id}', [AdminSuratPengantarController::class, 'update'])->name('p2k.surat-pengantar.update');
            Route::delete('/{id}', [AdminSuratPengantarController::class, 'destroy'])->name('p2k.surat-pengantar.destroy');
            Route::get('/{id}/cetak', [AdminSuratPengantarController::class, 'generatePdf'])->name('p2k.surat-pengantar.cetak');
            Route::get('/{id}/mahasiswa', [AdminSuratPengantarController::class, 'getMahasiswaList'])->name('p2k.surat-pengantar.mahasiswa');
        });

        // Laporan P2K Routes
        Route::prefix('laporan')->group(function () {
            Route::get('/', [LaporanP2KController::class, 'index'])->name('p2k.laporan.index');
            Route::get('/data', [LaporanP2KController::class, 'getData'])->name('p2k.laporan.data');
            Route::get('/{tahunAkademikId}/show', [LaporanP2KController::class, 'showKelas'])->name('p2k.laporan.show');
            Route::get('/data/{tahunAkademikId}', [LaporanP2KController::class, 'getKelasData'])->name('p2k.laporan.data.kelas');
            Route::get('/kelas/{id}', [LaporanP2KController::class, 'detailKelas'])->name('p2k.laporan.detail-kelas');
            Route::post('/validate-laporan', [LaporanP2KController::class, 'validateLaporanAkhir'])->name('p2k.laporan.validate');
        });

        // PKS Routes
        Route::prefix('pks')->group(function () {
            Route::get('/', [AdminPKSController::class, 'index'])->name('p2k.pks.index');
            Route::get('/data', [AdminPKSController::class, 'getData'])->name('p2k.pks.getData');
            Route::post('/', [AdminPKSController::class, 'store'])->name('p2k.pks.store');
            Route::post('/store-umkm', [AdminPKSController::class, 'storeUmkm'])->name('p2k.pks.storeUmkm');
            Route::post('/store-pks-umkm', [AdminPKSController::class, 'storePksUmkm'])->name('p2k.pks.storePksUmkm');
            Route::get('/arsip', [AdminPKSController::class, 'getArsip'])->name('p2k.pks.arsip');
            Route::get('/tanpa-pks', [AdminPKSController::class, 'getUmkmTanpaPks'])->name('p2k.pks.tanpa-pks');
            Route::get('/{id}', [AdminPKSController::class, 'show'])->name('p2k.pks.show');
            Route::put('/{id}', [AdminPKSController::class, 'update'])->name('p2k.pks.update');
            Route::delete('/{id}', [AdminPKSController::class, 'destroy'])->name('p2k.pks.destroy');
            Route::get('/{id}/cetak', [AdminPKSController::class, 'generatePdf'])->name('p2k.pks.cetak');

            Route::get('/umkm/{id}', [UmkmController::class, 'getDetail'])->name('p2k.pks.umkm.show');
        });

        // IA Routes
        Route::prefix('ia')->group(function () {
            Route::get('/', [IAController::class, 'index'])->name('p2k.ia.index');
            Route::get('/data', [IAController::class, 'data'])->name('p2k.ia.data');
            Route::get('/{id}', [IAController::class, 'show'])->name('p2k.ia.show');
        });

        // Kelas Dosen P2K Routes
        Route::prefix('kelas')->group(function () {
            Route::get('/', [KelasDosenP2KController::class, 'index'])->name('p2k.kelas.index');
            Route::get('/data', [KelasDosenP2KController::class, 'getData'])->name('p2k.kelas.data');
            Route::get('/{tahunAkademikId}/show', [KelasDosenP2KController::class, 'showKelas'])->name('p2k.kelas.show.tahun');
            Route::get('/data/{tahunAkademikId}', [KelasDosenP2KController::class, 'getKelasData'])->name('p2k.kelas.data.kelas');
            Route::post('/', [KelasDosenP2KController::class, 'store'])->name('p2k.kelas.store');
            Route::get('/{id}', [KelasDosenP2KController::class, 'show'])->name('p2k.kelas.show');
            Route::put('/{id}', [KelasDosenP2KController::class, 'update'])->name('p2k.kelas.update');
            Route::delete('/{id}', [KelasDosenP2KController::class, 'destroy'])->name('p2k.kelas.destroy');
        });

        // Dosen P2K Routes
        Route::prefix('dosen')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DosenP2KController::class, 'index'])->name('p2k.dosen.index');
            Route::get('/data', [\App\Http\Controllers\Admin\DosenP2KController::class, 'getData'])->name('p2k.dosen.data');
            Route::get('/api', [\App\Http\Controllers\Admin\DosenP2KController::class, 'getDosenFromApi'])->name('p2k.dosen.api');
            Route::post('/', [\App\Http\Controllers\Admin\DosenP2KController::class, 'store'])->name('p2k.dosen.store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\DosenP2KController::class, 'show'])->name('p2k.dosen.show');
            Route::put('/{id}', [\App\Http\Controllers\Admin\DosenP2KController::class, 'update'])->name('p2k.dosen.update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\DosenP2KController::class, 'destroy'])->name('p2k.dosen.destroy');
        });
    });

    // UMKM Routes - Untuk Admin dan Mahasiswa
    Route::prefix('master')->middleware('role:admin,mahasiswa,tamu')->group(function () {
        Route::prefix('umkm')->group(function () {
            Route::get('/', [UmkmController::class, 'index'])->name('master.umkm.index');
            Route::get('/data', [UmkmController::class, 'getData'])->name('master.umkm.data');
            Route::post('/', [UmkmController::class, 'store'])->name('master.umkm.store');
            Route::get('/{id}', [UmkmController::class, 'show'])->name('master.umkm.show');
            Route::put('/{id}', [UmkmController::class, 'update'])->name('master.umkm.update');
            Route::delete('/{id}', [UmkmController::class, 'destroy'])->name('master.umkm.destroy');
        });
    });



    // Include Dosen P2K Routes
    require __DIR__ . '/web-dosen-p2k.php';

    // Mahasiswa Routes - Hanya untuk Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->group(function () {
        // Data UMKM Routes
        Route::prefix('data-umkm')->group(function () {
            Route::get('/', [DataUmkmController::class, 'index'])->name('mahasiswa.data-umkm.index');
            Route::get('/data', [DataUmkmController::class, 'getData'])->name('mahasiswa.data-umkm.data');
            Route::get('/arsip', [DataUmkmController::class, 'getArsip'])->name('mahasiswa.data-umkm.arsip');
            Route::get('/tanpa-pks', [DataUmkmController::class, 'getUmkmTanpaPks'])->name('mahasiswa.data-umkm.tanpa-pks');
            Route::post('/', [DataUmkmController::class, 'store'])->name('mahasiswa.data-umkm.store');
            Route::get('/{id}', [DataUmkmController::class, 'show'])->name('mahasiswa.data-umkm.show');
            Route::put('/{id}', [DataUmkmController::class, 'update'])->name('mahasiswa.data-umkm.update');
            Route::delete('/{id}', [DataUmkmController::class, 'destroy'])->name('mahasiswa.data-umkm.destroy');
        });

        // Surat Pengantar Routes
        Route::prefix('surat-pengantar')->group(function () {
            Route::get('/', [SuratPengantarController::class, 'index'])->name('mahasiswa.surat-pengantar.index');
            Route::get('/data', [SuratPengantarController::class, 'getData'])->name('mahasiswa.surat-pengantar.data');
            Route::post('/', [SuratPengantarController::class, 'store'])->name('mahasiswa.surat-pengantar.store');
            Route::get('/{id}', [SuratPengantarController::class, 'show'])->name('mahasiswa.surat-pengantar.show');
            Route::put('/{id}', [SuratPengantarController::class, 'update'])->name('mahasiswa.surat-pengantar.update');
            Route::delete('/{id}', [SuratPengantarController::class, 'destroy'])->name('mahasiswa.surat-pengantar.destroy');
            Route::get('/{id}/cetak', [SuratPengantarController::class, 'generatePdf'])->name('mahasiswa.surat-pengantar.cetak');
        });

        // PKS Routes
        Route::prefix('pks')->group(function () {
            Route::get('/', [PKSController::class, 'index'])->name('mahasiswa.pks.index');
            Route::get('/data', [PKSController::class, 'getData'])->name('mahasiswa.pks.getData');
            Route::post('/', [PKSController::class, 'store'])->name('mahasiswa.pks.store');
            Route::get('/{id}', [PKSController::class, 'show'])->name('mahasiswa.pks.show');
            Route::post('/{id}', [PKSController::class, 'update'])->name('mahasiswa.pks.update');
            Route::delete('/{id}', [PKSController::class, 'destroy'])->name('mahasiswa.pks.destroy');
            Route::get('/{id}/cetak', [PKSController::class, 'generatePdf'])->name('mahasiswa.pks.cetak');
            Route::post('/{id}/upload', [PKSController::class, 'uploadFile'])->name('mahasiswa.pks.uploadFile');
        });

        // Laporan Akhir Routes
        Route::prefix('laporan-akhir')->group(function () {
            Route::get('/', [LaporanAkhirController::class, 'index'])->name('mahasiswa.laporan-akhir.index');
            Route::get('/data', [LaporanAkhirController::class, 'getData'])->name('mahasiswa.laporan-akhir.data');
            Route::get('/get-surat-pengantar', [LaporanAkhirController::class, 'getSuratPengantar'])->name('mahasiswa.get-surat-pengantar');
            Route::post('/', [LaporanAkhirController::class, 'store'])->name('mahasiswa.laporan-akhir.store');
            Route::get('/{id}', [LaporanAkhirController::class, 'show'])->name('mahasiswa.laporan-akhir.show');
            Route::put('/{id}', [LaporanAkhirController::class, 'update'])->name('mahasiswa.laporan-akhir.update');
            Route::delete('/{id}', [LaporanAkhirController::class, 'destroy'])->name('mahasiswa.laporan-akhir.destroy');
        });
    });

    // Ujian Routes - Only accessible by mahasiswa
    Route::middleware('role:mahasiswa')->group(function () {
        Route::prefix('ujian')->group(function () {
            Route::get('/', [UjianController::class, 'index'])->name('ujian.index');

            // Encrypted routes only (secure implementation)
            Route::get('/start/{encryptedId}', [UjianController::class, 'startExamEncrypted'])->name('ujian.start');
            Route::get('/exam/{encryptedId}', [UjianController::class, 'showEncrypted'])->name('ujian.show');
            Route::get('/result/{encryptedId}', [UjianController::class, 'resultEncrypted'])->name('ujian.result');

            // API routes (unchanged)
            Route::post('/submit-answer', [UjianController::class, 'submitAnswer'])->name('ujian.submitAnswer');
            Route::post('/finish', [UjianController::class, 'finishExam'])->name('ujian.finish');
            Route::post('/timeout-submit', [UjianController::class, 'timeoutSubmit'])->name('ujian.timeoutSubmit');
            Route::post('/generate-encrypted-url', [UjianController::class, 'generateEncryptedUrl'])->name('ujian.generateEncryptedUrl');
        });
    });
});

// API Routes for AJAX calls
// Route::get('/api/program-studi', function (Request $request) {
//     $query = \App\Models\ProgramStudi::with('fakultas');

//     if ($request->filled('fakultas_id')) {
//         $query->where('fakultas_id', $request->fakultas_id);
//     }

//     return response()->json($query->orderBy('nama_prodi')->get());
// });

Route::get('/make-symlink', function () {
    $target = storage_path('app/public');      // folder asli
    $link = public_path('storage');            // link di folder public

    if (file_exists($link)) {
        return 'Symlink sudah ada.';
    }

    try {
        symlink($target, $link);
        return 'Symlink berhasil dibuat.';
    } catch (\Exception $e) {
        return 'Gagal membuat symlink: ' . $e->getMessage();
    }
});
