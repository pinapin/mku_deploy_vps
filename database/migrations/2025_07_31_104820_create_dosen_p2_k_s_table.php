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
        Schema::create('dosen_p2_k_s', function (Blueprint $table) {
            $table->id();
            $table->string('kode_dosen')->index();
            $table->string('nama_dosen');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_p2_k_s');
    }
};
