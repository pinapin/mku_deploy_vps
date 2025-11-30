<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ujian;
use Faker\Factory as Faker;
use App\Models\Soal;
use App\Models\Pilihan;

class UjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ujian = Ujian::create([
            'nama_ujian' => 'Tes Kewirausahaan Dasar',
            'durasi_menit' => 30,
            'deskripsi' => 'Tes otomatis 100 soal',
            'is_active' => true
        ]);

        for ($i = 1; $i <= 100; $i++) {

            // buat soal
            $soal = Soal::factory()->create([
                'id_ujian' => $ujian->id,
                'nomor_soal' => $i
            ]);

            // generate huruf pilihan
            $huruf = ['A', 'B', 'C', 'D'];
            $faker = Faker::create();
            $jawaban_benar = $faker->randomElement($huruf);

            foreach ($huruf as $h) {
                Pilihan::factory()->create([
                    'id_soal' => $soal->id,
                    'huruf_pilihan' => $h,
                    'is_benar' => $h === $jawaban_benar
                ]);
            }
        }
    }
}
