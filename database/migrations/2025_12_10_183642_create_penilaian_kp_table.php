<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('penilaian_kp', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('mahasiswa_id');

        $table->integer('nilai_laporan');
        $table->integer('nilai_presentasi');
        $table->integer('nilai_aktivitas_kp');

        $table->integer('nilai_akhir')->nullable();

        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('penilaian_kp');
    }
};
