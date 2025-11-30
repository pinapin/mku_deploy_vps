<?php

namespace App\Providers;

use App\Models\DosenP2K;
use App\Models\Fakultas;
use App\Models\KategoriUmkm;
use App\Models\KelasDosenP2K;
use App\Models\LaporanAkhirMahasiswa;
use App\Models\Mahasiswa;
use App\Models\PKS;
use App\Models\ProgramStudi;
use App\Models\SertifikatKwu;
use App\Models\SesiUjian;
use App\Models\SettingSuratPengantar;
use App\Models\SuratPengantar;
use App\Models\SuratPengantarMahasiswa;
use App\Models\TahunAkademik;
use App\Models\Umkm;
use App\Models\User;
use App\Observers\ModelObserver;
use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        User::observe(ModelObserver::class);
        Mahasiswa::observe(ModelObserver::class);
        DosenP2K::observe(ModelObserver::class);
        Fakultas::observe(ModelObserver::class);
        KategoriUmkm::observe(ModelObserver::class);
        KelasDosenP2K::observe(ModelObserver::class);
        LaporanAkhirMahasiswa::observe(ModelObserver::class);
        PKS::observe(ModelObserver::class);
        ProgramStudi::observe(ModelObserver::class);
        SertifikatKwu::observe(ModelObserver::class);
        SesiUjian::observe(ModelObserver::class);
        SettingSuratPengantar::observe(ModelObserver::class);
        SuratPengantar::observe(ModelObserver::class);
        SuratPengantarMahasiswa::observe(ModelObserver::class);
        TahunAkademik::observe(ModelObserver::class);
        Umkm::observe(ModelObserver::class);
    }
}
