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
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_umkm_id')->constrained('kategori_umkms')->onDelete('cascade');
            $table->string('input_by')->nullable()->foreign('mahasiswas')->references('nim')->cascadeOnUpdate()->nullOnDelete()->comment('berisi nim mahasiswa');
            $table->string('nama_umkm');
            $table->string('nama_pemilik_umkm');
            $table->string('jabatan_umkm');
            $table->string('no_hp_umkm');
            $table->string('email_umkm');
            $table->string('alamat_umkm');
            $table->string('logo_umkm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
