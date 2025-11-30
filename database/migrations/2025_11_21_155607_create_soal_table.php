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
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ujian')->constrained('ujian')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('teks_soal');
            $table->enum('tipe', ['pilihan_ganda'])->default('pilihan_ganda');
            $table->integer('nomor_soal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
