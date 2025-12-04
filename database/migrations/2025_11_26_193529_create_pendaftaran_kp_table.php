<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendaftaranKpTable extends Migration
{
    public function up()
    {
        Schema::create('pendaftaran_kp', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mahasiswa');
            $table->string('nim')->unique();
            $table->string('email')->unique();
            $table->string('no_hp')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('universitas')->nullable();
            $table->string('tema_kp')->nullable();
            $table->string('perusahaan')->nullable();
            $table->string('krs')->nullable();
            $table->string('transkrip')->nullable();
            $table->string('proposal')->nullable();
            $table->enum('status_validasi', ['pending', 'valid', 'invalid'])->default('pending');
            $table->text('catatan_validasi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pendaftaran_kp');
    }
}