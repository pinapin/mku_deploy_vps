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
        Schema::create('kelas_dosen_p2_k_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('kode_dosen')->foreign('dosen_p2_k_s')->references('kode_dosen')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('kelas')->length(2)->comment('01,02,03,04...');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_dosen_p2_k_s');
    }
};
