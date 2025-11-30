<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'jawaban_mahasiswa';

    protected $fillable = [
        'id_sesi',
        'id_soal',
        'id_pilihan_dipilih'
    ];

    public function sesiUjian()
    {
        return $this->belongsTo(SesiUjian::class, 'id_sesi');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function pilihanDipilih()
    {
        return $this->belongsTo(Pilihan::class, 'id_pilihan_dipilih');
    }

    public function isBenar()
    {
        return $this->pilihanDipilih && $this->pilihanDipilih->is_benar;
    }
}
