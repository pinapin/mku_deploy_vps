<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'nim';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nim',
        'nama',
        'prodi_id',
        'tahun_akademik_id'
    ];

    /**
     * Get the program studi that owns the mahasiswa.
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }
    
    /**
     * Get the login logs for the mahasiswa.
     */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class, 'user_id')->where('role', 'mahasiswa');
    }

    /**
     * Get the surat pengantars for the mahasiswa.
     */
    public function suratPengantars()
    {
        return $this->hasMany(SuratPengantar::class, 'input_by', 'nim');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }
}
