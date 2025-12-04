<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catatan_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('isi_catatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_bimbingans');
    }
};
