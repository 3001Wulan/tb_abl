<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pengantar_kp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_kp_id');
            $table->string('nomor_surat')->unique();
            
            // Data Mahasiswa
            $table->string('nama_mahasiswa');
            $table->string('nim');
            $table->string('jurusan');
            $table->string('universitas');
            
            // Data Penempatan
            $table->string('nama_perusahaan');
            $table->string('alamat_perusahaan')->nullable();
            $table->string('kontak_perusahaan')->nullable();
            
            // Data Pembimbing
            $table->string('nama_pembimbing_akademik')->nullable();
            $table->string('nip_pembimbing')->nullable();
            
            // Status Surat
            $table->enum('status_pengajuan', ['pending', 'proses', 'selesai', 'ditolak'])->default('pending');
            $table->enum('status_penandatanganan', ['menunggu', 'sudah_ditandatangani', 'batal'])->default('menunggu');
            
            // File PDF
            $table->string('file_path')->nullable();
            $table->string('nama_file_pdf')->nullable();
            
            // Data Penandatanganan
            $table->string('nama_penandatangan')->nullable();
            $table->string('jabatan_penandatangan')->nullable();
            $table->date('tanggal_penandatanganan')->nullable();
            
            // Catatan
            $table->text('catatan_pengajuan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            
            // Waktu
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            $table->timestamps();
            
            // Foreign key
            $table->foreign('pendaftaran_kp_id')->references('id')->on('pendaftaran_kp')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengantar_kp');
    }
};