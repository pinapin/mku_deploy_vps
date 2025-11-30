<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenP2K extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode_dosen',
        'nama_dosen',
        'tahun_akademik_id'
    ];
    
    /**
     * Get the tahun akademik that owns the dosen.
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
    
    /**
     * Get the login logs for the dosen.
     */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class, 'user_id')->where('role', 'dosen');
    }
    
    /**
     * Get the kelas for the dosen.
     */
    public function kelasDosenP2Ks()
    {
        return $this->hasMany(KelasDosenP2K::class, 'kode_dosen', 'kode_dosen');
    }
}
