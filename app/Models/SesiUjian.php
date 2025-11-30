<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiUjian extends Model
{
    use HasFactory;

    protected $table = 'sesi_ujian';

    protected $fillable = [
        'id_ujian',
        'nim',
        'waktu_mulai',
        'waktu_selesai',
        'skor_akhir',
        'status'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime'
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'id_ujian');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim');
    }

    public function jawabanMahasiswa()
    {
        return $this->hasMany(JawabanMahasiswa::class, 'id_sesi');
    }

    public function getRemainingTimeAttribute()
    {
        if ($this->waktu_selesai) {
            return 0;
        }

        // Make sure ujian relationship is loaded
        if (!$this->relationLoaded('ujian')) {
            $this->load('ujian');
        }

        if (!$this->ujian) {
            return 0;
        }

        $endTime = $this->waktu_mulai->copy()->addMinutes($this->ujian->durasi_menit);
        $now = now();

        if ($now >= $endTime) {
            return 0;
        }

        return $endTime->diffInSeconds($now);
    }

    public function isTimedOut()
    {
        return $this->getRemainingTimeAttribute() <= 0 && !$this->waktu_selesai;
    }
}
