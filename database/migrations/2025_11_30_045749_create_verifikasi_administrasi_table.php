<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_administrasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_kp_id');
            $table->string('nomor_verifikasi')->unique();
            
            // Data Mahasiswa
            $table->string('nama_mahasiswa');
            $table->string('nim');
            $table->string('jurusan');
            
            // Cek Kelengkapan Berkas
            $table->boolean('krs_lengkap')->default(false);
            $table->boolean('transkrip_lengkap')->default(false);
            $table->boolean('proposal_lengkap')->default(false);
            $table->text('catatan_kelengkapan')->nullable();
            
            // Status Verifikasi
            $table->enum('status_verifikasi', ['pending', 'lengkap', 'tidak_lengkap'])->default('pending');
            $table->enum('status_persetujuan', ['pending', 'disetujui', 'ditolak'])->default('pending');
            
            // Data Persetujuan
            $table->text('alasan_tolak')->nullable();
            $table->string('nama_pemeriksa')->nullable();
            $table->date('tanggal_verifikasi')->nullable();
            $table->date('tanggal_persetujuan')->nullable();
            
            // Status Ke Mahasiswa
            $table->string('status_mahasiswa')->default('menunggu verifikasi');
            $table->text('pesan_ke_mahasiswa')->nullable();
            
            $table->timestamps();
            
            // Foreign key
            $table->foreign('pendaftaran_kp_id')->references('id')->on('pendaftaran_kp')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_administrasi');
    }
};