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
        Schema::create('informasi_kp', function (Blueprint $table) {
            $table->id();
            
            // Tipe informasi: syarat, jadwal, template, prosedur
            $table->enum('tipe', ['syarat', 'jadwal', 'template', 'prosedur']);
            
            // Field umum untuk semua tipe
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            
            // Field khusus SYARAT
            $table->boolean('is_wajib')->nullable();
            
            // Field khusus JADWAL
            $table->string('tahun_akademik')->nullable();
            $table->enum('periode', ['ganjil', 'genap'])->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            // Field khusus TEMPLATE
            $table->enum('jenis_dokumen', [
                'surat_pengantar',
                'surat_balasan',
                'laporan',
                'form_penilaian',
                'surat_tugas',
                'lainnya'
            ])->nullable();
            $table->string('file_path')->nullable();
            
            // Field khusus PROSEDUR
            $table->integer('urutan')->nullable();
            $table->string('estimasi_waktu')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_kp');
    }
};