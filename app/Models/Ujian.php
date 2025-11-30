<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';

    protected $fillable = [
        'nama_ujian',
        'durasi_menit',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function soal()
    {
        return $this->hasMany(Soal::class, 'id_ujian');
    }

    public function sesiUjian()
    {
        return $this->hasMany(SesiUjian::class, 'id_ujian');
    }
}
