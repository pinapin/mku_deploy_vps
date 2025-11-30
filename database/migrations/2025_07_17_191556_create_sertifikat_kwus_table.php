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
        Schema::create('sertifikat_kwus', function (Blueprint $table) {
            $table->id();
            $table->string('no_sertifikat')->length(20)->unique();
            $table->date('tgl_sertifikat');
            $table->string('nim')->length(9);
            $table->string('nama');
            $table->foreignId('prodi_id')->constrained('program_studis')->cascadeOnUpdate()->nullable();
            $table->string('semester');
            $table->string('tahun');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat_kwus');
    }
};
