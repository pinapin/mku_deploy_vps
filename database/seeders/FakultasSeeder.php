<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use Illuminate\Database\Seeder;

class FakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data yang ada terlebih dahulu
        // Fakultas::truncate();

        // Data fakultas
        // tambahkan id fakultas
        $fakultas = [
            ['id' => 1, 'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis'],
            ['id' => 2, 'nama_fakultas' => 'Fakultas Hukum'],
            ['id' => 3, 'nama_fakultas' => 'Fakultas Keguruan dan Ilmu Pendidikan'],
            ['id' => 4, 'nama_fakultas' => 'Fakultas Pertanian'],
            ['id' => 5, 'nama_fakultas' => 'Fakultas Psikologi'],
            ['id' => 6, 'nama_fakultas' => 'Fakultas Teknik'],
        ];

        // Insert data fakultas
        foreach ($fakultas as $fak) {
            Fakultas::create($fak);
        }
    }
}
