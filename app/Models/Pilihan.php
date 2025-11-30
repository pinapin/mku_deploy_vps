<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    use HasFactory;

    protected $table = 'pilihan';

    protected $fillable = [
        'id_soal',
        'teks_pilihan',
        'huruf_pilihan',
        'is_benar'
    ];

    protected $casts = [
        'is_benar' => 'boolean'
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function jawabanMahasiswa()
    {
        return $this->hasMany(JawabanMahasiswa::class, 'id_pilihan_dipilih');
    }
}
