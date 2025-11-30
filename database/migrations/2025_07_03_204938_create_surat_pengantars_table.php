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
        Schema::create('surat_pengantars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('input_by');
            $table->string('kelas')->length(2)->comment('01,02,03,04...');
            $table->integer('kelompok')->length(2)->comment('1,2,3,4...');
            $table->foreign('input_by')->references('nim')->on('mahasiswas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('umkm_id')->constrained('umkms', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tgl_surat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pengantars');
    }
};
