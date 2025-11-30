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
        Schema::table('soal', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('tipe');

            // Add new enum column with both pilihan_ganda and essay
            $table->enum('tipe', ['pilihan_ganda', 'essay'])->default('pilihan_ganda');

            // Add kunci_jawaban field for essay questions
            $table->text('kunci_jawaban')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            // Remove kunci_jawaban
            $table->dropColumn('kunci_jawaban');

            // Revert tipe to original enum
            $table->dropColumn('tipe');
            $table->enum('tipe', ['pilihan_ganda'])->default('pilihan_ganda');
        });
    }
};
