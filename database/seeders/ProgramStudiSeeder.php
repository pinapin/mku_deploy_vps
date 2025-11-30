<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data yang ada terlebih dahulu
        // ProgramStudi::truncate();

        // Data program studi
        $programStudis = [
            ['id' => 1, 'nama_prodi' => 'Agribisnis', 'fakultas_id' => 4],
            ['id' => 2, 'nama_prodi' => 'Agroteknologi', 'fakultas_id' => 4],
            ['id' => 3, 'nama_prodi' => 'Akuntansi', 'fakultas_id' => 1],
            ['id' => 4, 'nama_prodi' => 'Ilmu Hukum', 'fakultas_id' => 2],
            ['id' => 5, 'nama_prodi' => 'Manajemen', 'fakultas_id' => 1],
            ['id' => 6, 'nama_prodi' => 'Pendidikan Bahasa Inggris', 'fakultas_id' => 3],
            ['id' => 7, 'nama_prodi' => 'Pendidikan Guru Sekolah Dasar', 'fakultas_id' => 3],
            ['id' => 8, 'nama_prodi' => 'Pendidikan Matematika', 'fakultas_id' => 3],
            ['id' => 9, 'nama_prodi' => 'Psikologi', 'fakultas_id' => 5],
            ['id' => 10, 'nama_prodi' => 'Sistem Informasi', 'fakultas_id' => 6],
            ['id' => 11, 'nama_prodi' => 'Teknik Elektro', 'fakultas_id' => 6],
            ['id' => 12, 'nama_prodi' => 'Teknik Industri', 'fakultas_id' => 6],
            ['id' => 13, 'nama_prodi' => 'Teknik Informatika', 'fakultas_id' => 6],
            ['id' => 14, 'nama_prodi' => 'Teknik Mesin', 'fakultas_id' => 6],
            ['id' => 15, 'nama_prodi' => 'Bimbingan dan Konseling', 'fakultas_id' => 3],
            ['id' => 16, 'nama_prodi' => 'Pendidikan Bahasa Dan Sastra Indonesia', 'fakultas_id' => 3],
        ];

        // Insert data program studi
        foreach ($programStudis as $prodi) {
            ProgramStudi::create($prodi);
        }
    }
}
