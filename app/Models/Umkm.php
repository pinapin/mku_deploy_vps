<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Umkm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kategori_umkm_id',
        'input_by',
        'nama_umkm',
        'nama_pemilik_umkm',
        'jabatan_umkm',
        'no_hp_umkm',
        'email_umkm',
        'alamat_umkm',
        'logo_umkm'
    ];

    /**
     * Get the kategori umkm that owns the umkm.
     */
    public function kategoriUmkm()
    {
        return $this->belongsTo(KategoriUmkm::class);
    }

    /**
     * Get the mahasiswa that owns the umkm.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'input_by', 'nim');
    }
}
