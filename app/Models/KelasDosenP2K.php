<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasDosenP2K extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tahun_akademik_id',
        'kode_dosen',
        'kelas'
    ];
    
    /**
     * Get the tahun akademik that owns the kelas.
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
    
    /**
     * Get the dosen that owns the kelas.
     */
    public function dosen()
    {
        return $this->belongsTo(DosenP2K::class, 'kode_dosen', 'kode_dosen');
    }
    
    /**
     * Get the laporan akhir mahasiswas for the kelas.
     */
    public function laporanAkhirMahasiswas()
    {
        return $this->hasMany(LaporanAkhirMahasiswa::class, 'kelas_dosen_p2k_id');
    }
}
