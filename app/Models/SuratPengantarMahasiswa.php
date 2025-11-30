<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratPengantarMahasiswa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'surat_pengantar_id',
        'nim',
        'nama_mahasiswa',
        'prodi_id'
    ];

    /**
     * Get the surat pengantar that owns the mahasiswa.
     */
    public function suratPengantar()
    {
        return $this->belongsTo(SuratPengantar::class);
    }

    /**
     * Get the program studi that owns the mahasiswa.
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    /**
     * Get the mahasiswa associated with the surat pengantar mahasiswa.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim');
    }

    /**
     * Get the laporan akhir associated with the surat pengantar mahasiswa.
     */
}
