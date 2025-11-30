<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fakultas extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_fakultas'
    ];
    
    /**
     * Get the program studis for the fakultas.
     */
    public function programStudis()
    {
        return $this->hasMany(ProgramStudi::class, 'fakultas_id');
    }
}
