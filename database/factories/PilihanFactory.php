<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pilihan>
 */
class PilihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_soal' => null,
            'teks_pilihan' => $this->faker->sentence(6),
            'huruf_pilihan' => null,
            'is_benar' => false
        ];
    }
}
