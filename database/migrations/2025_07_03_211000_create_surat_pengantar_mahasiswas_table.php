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
        Schema::create('surat_pengantar_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_pengantar_id')->constrained('surat_pengantars')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nim');
            $table->string('nama_mahasiswa');
            $table->foreignId('prodi_id')->constrained('program_studis')->cascadeOnUpdate()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pengantar_mahasiswas');
    }
};
