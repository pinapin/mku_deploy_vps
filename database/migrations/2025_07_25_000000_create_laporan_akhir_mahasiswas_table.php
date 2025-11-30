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
        Schema::create('laporan_akhir_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('kelompok');
            $table->string('kelas');
            $table->foreignId('kelas_dosen_p2k_id')->constrained('kelas_dosen_p2_k_s')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_pks')->nullable();
            $table->string('file_ia')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->text('catatan_validasi')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('validated_by')->nullable()->foreign('dosen_p2_k_s')->references('kode_dosen')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_akhir_mahasiswas');
    }
};