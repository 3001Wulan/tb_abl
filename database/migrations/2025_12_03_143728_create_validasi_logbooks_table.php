<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validasi_logbooks', function (Blueprint $table) {
            $table->id();
            
            // Kunci Asing ke tabel logbook_k_p_s
            $table->foreignId('logbook_kp_id')->constrained('logbook_k_p_s')->onDelete('cascade');
            // Kunci Asing ke tabel lecturers
            $table->foreignId('lecturer_id')->constrained('lecturers'); 
            
            $table->enum('status_validasi', ['Disetujui', 'Ditolak']);
            $table->text('catatan_pembimbing')->nullable();
            
            $table->timestamps();

            $table->unique('logbook_kp_id'); // Setiap logbook hanya bisa divalidasi 1 kali
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_logbooks');
    }
};