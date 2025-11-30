<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanAkhirMahasiswa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tahun_akademik_id',
        'nim',
        'kelompok',
        'kelas',
        'kelas_dosen_p2k_id',
        'file_path',
        'file_pks',
        'file_ia',
        'is_validated',
        'catatan_validasi',
        'validated_at',
        'validated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the tahun akademik that owns the laporan.
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    /**
     * Get the mahasiswa that owns the laporan.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    /**
     * Get the kelas dosen that owns the laporan.
     */
    public function kelasDosenP2K()
    {
        return $this->belongsTo(KelasDosenP2K::class, 'kelas_dosen_p2k_id');
    }

    /**
     * Get the dosen that validated the laporan.
     */
    public function validator()
    {
        return $this->belongsTo(DosenP2K::class, 'validated_by', 'kode_dosen');
    }
}