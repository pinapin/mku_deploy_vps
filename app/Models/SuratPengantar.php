<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratPengantar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tahun_akademik_id',
        'input_by',
        'kelas',
        'kelompok',
        'umkm_id',
        'tgl_surat'
    ];

    /**
     * Get the UMKM that owns the surat pengantar.
     */
    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }

    /**
     * Get the mahasiswa that input the surat pengantar.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'input_by', 'nim');
    }

    /**
     * Get the mahasiswas for the surat pengantar.
     */
    public function suratPengantarMahasiswas()
    {
        return $this->hasMany(SuratPengantarMahasiswa::class);
    }

    /**
     * Get the tahun akademik that owns the surat pengantar.
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    // public function laporanAkhir()
    // {
    //     return $this->hasOne(LaporanAkhirMahasiswa::class, 'nim', 'nim')
    //         ->where('tahun_akademik_id', $this->tahun_akademik_id);
    // }
}
