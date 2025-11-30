<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Fakultas;

class SertifikatKwu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_sertifikat',
        'tgl_sertifikat',
        'nim',
        'nama',
        'prodi_id',
        'semester',
        'tahun',
        'keterangan'
    ];

    /**
     * Get the program studi that owns the sertifikat kwu.
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    /**
     * Get the fakultas through program studi.
     */
    public function fakultas()
    {
        return $this->hasOneThrough(
            Fakultas::class,
            ProgramStudi::class,
            'id', // Foreign key on program_studis table
            'id', // Foreign key on fakultas table
            'prodi_id', // Local key on sertifikat_kwus table
            'fakultas_id' // Local key on program_studis table
        );
    }
}
