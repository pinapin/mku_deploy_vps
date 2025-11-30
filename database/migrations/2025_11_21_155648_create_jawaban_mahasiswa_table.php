<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jawaban_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sesi')->constrained('sesi_ujian')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_soal')->constrained('soal')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_pilihan_dipilih')->nullable()->constrained('pilihan')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_sesi', 'id_soal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_mahasiswa');
    }
};
