<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PKS extends Model
{
    use HasFactory;

    protected $table = 'p_k_s';
    
    protected $fillable = [
        'tahun_akademik_id',
        'tgl_pks',
        'no_pks',
        'no_pks_umkm',
        'file_arsip_pks',
        'umkm_id',
        'lama_perjanjian',
        'pic_pks',
        'email_pks',
        'alamat_pks',
        'created_by'
    ];

    protected $appends = ['nama_umkm'];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'created_by', 'nim');
    }

    public function getNamaUmkmAttribute()
    {
        return $this->umkm ? $this->umkm->nama_umkm : null;
    }
}
