<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('riwayat_revisi_laporan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_kp_id');
            $table->foreign('laporan_kp_id')->references('id')->on('laporan_kp')->onDelete('cascade');
            $table->integer('versi'); // 1, 2, 3, dst
            $table->string('file_path');
            $table->text('keterangan')->nullable(); // apa yang direvisi
            $table->boolean('is_format_valid')->default(true);
            $table->boolean('is_final')->default(false); // versi final/approved
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['laporan_kp_id', 'versi']);
            $table->unique(['laporan_kp_id', 'versi']); // 1 laporan gak boleh ada versi duplicate
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_revisi_laporan');
    }
};