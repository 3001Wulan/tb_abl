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
    Schema::create('konsultasi_kp', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('mahasiswa_id');
        $table->unsignedBigInteger('dosen_id');
        $table->date('tanggal_bimbingan');
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi_kp');
    }
};
