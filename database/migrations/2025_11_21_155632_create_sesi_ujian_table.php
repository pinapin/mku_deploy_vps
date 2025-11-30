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
        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ujian')->constrained('ujian')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nim', 20); // Foreign key to mahasiswas
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();
            $table->integer('skor_akhir')->nullable();
            $table->enum('status', ['berlangsung', 'selesai', 'scoring', 'timeout'])->default('berlangsung');
            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswas')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_ujian');
    }
};
