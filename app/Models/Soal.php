<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $table = 'soal';

    protected $fillable = [
        'id_ujian',
        'teks_soal',
        'tipe',
        'nomor_soal',
        'kunci_jawaban'
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'id_ujian');
    }

    public function pilihan()
    {
        return $this->hasMany(Pilihan::class, 'id_soal');
    }

    public function jawabanMahasiswa()
    {
        return $this->hasMany(JawabanMahasiswa::class, 'id_soal');
    }

    public function pilihanBenar()
    {
        return $this->hasOne(Pilihan::class, 'id_soal')->where('is_benar', true);
    }
}
