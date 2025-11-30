<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramStudi extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_prodi',
        'fakultas_id'
    ];
    
    /**
     * Get the surat pengantar mahasiswas for the program studi.
     */
    public function suratPengantarMahasiswas()
    {
        return $this->hasMany(SuratPengantarMahasiswa::class, 'prodi_id');
    }
    
    /**
     * Get the fakultas that owns the program studi.
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }
}
