<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_k_p_s', function (Blueprint $table) {
            $table->id();

            // Kunci Asing ke tabel students
            $table->foreignId('student_id')->constrained('students'); 
            
            $table->unsignedSmallInteger('minggu_ke'); 
            $table->date('tanggal_mulai');
            $table->text('deskripsi_kegiatan');

            // Tambahan untuk upload file
            $table->string('file_kegiatan')->nullable(); 
            
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->timestamps();

            // Satu minggu hanya satu logbook per siswa
            $table->unique(['student_id', 'minggu_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_k_p_s');
    }
};
