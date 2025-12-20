<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename table
        Schema::rename('pengumpulan_laporan_kp', 'laporan_kp');

        // Drop kolom plagiarism_score
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->dropColumn('plagiarism_score');
        });
    }

    public function down(): void
    {
        // Tambah lagi kolom plagiarism_score
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->integer('plagiarism_score')->nullable();
        });

        // Balikin nama tabel
        Schema::rename('laporan_kp', 'pengumpulan_laporan_kp');
    }
};
