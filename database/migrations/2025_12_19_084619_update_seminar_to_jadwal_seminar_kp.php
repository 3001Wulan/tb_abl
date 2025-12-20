<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSeminarToJadwalSeminarKp extends Migration
{
    public function up()
    {
        // 1. Hapus tabel pivot examiners
        Schema::dropIfExists('seminar_examiners');

        // 2. Rename tabel seminars -> jadwal_seminar_kp
        Schema::rename('seminars', 'jadwal_seminar_kp');
    }

    public function down()
    {
        // Balikin nama tabel
        Schema::rename('jadwal_seminar_kp', 'seminars');

        // Balikin tabel examiners
        Schema::create('seminar_examiners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seminar_id')->constrained('seminars')->onDelete('cascade');
            $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['primary','secondary'])->default('primary');
            $table->timestamps();
            $table->unique(['seminar_id','examiner_id']);
        });
    }
}
