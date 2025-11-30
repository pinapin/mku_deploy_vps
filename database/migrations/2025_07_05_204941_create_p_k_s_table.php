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
        Schema::create('p_k_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tgl_pks');
            $table->string('no_pks')->comment('Nomor PKS Otomatis');
            $table->string('no_pks_umkm')->comment('Nomor PKS dari UMKM');
            $table->foreignId('umkm_id')->constrained('umkms')->cascadeOnUpdate()->cascadeOnDelete();
            $table->tinyInteger('lama_perjanjian')->default(1)->comment('default 1 tahun');
            $table->string('pic_pks');
            $table->string('email_pks');
            $table->string('alamat_pks');
            $table->string('created_by')->foreign('mahasiswas')->references('nim')->cascadeOnUpdate()->nullOnDelete()->comment('NIM mahasiswa yang membuat PKS');
            $table->string('file_arsip_pks')->nullable()->comment('File arsip PKS di upload Admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_k_s');
    }
};
