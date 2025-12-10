<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_kp', function (Blueprint $table) {
            $table->id();

            // Relasi ke logbook KP
            $table->unsignedBigInteger('logbook_kp_id');
            $table->foreign('logbook_kp_id')
                  ->references('id')
                  ->on('logbook_k_p_s')
                  ->onDelete('cascade');

            // Relasi ke dosen (optional, bisa ubah kalau nama tabel beda)
            $table->unsignedBigInteger('lecturer_id');

            // Isi evaluasi tengah KP
            $table->text('progres_pencapaian');
            $table->string('penilaian_sementara', 50);
            $table->text('catatan_pembimbing')->nullable();
            $table->text('rekomendasi_lanjutan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_kp');
    }
};
