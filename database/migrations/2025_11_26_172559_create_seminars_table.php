<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeminarsTable extends Migration
{
    public function up()
    {
        Schema::create('seminars', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // judul KP / topik
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('scheduled_at')->nullable(); // jadwal presentasi
            $table->enum('status', ['pending','scheduled','done','cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('seminar_examiners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seminar_id')->constrained('seminars')->onDelete('cascade');
            $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade'); // dosen penguji
            $table->enum('role', ['primary','secondary'])->default('primary');
            $table->timestamps();
            $table->unique(['seminar_id','examiner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('seminar_examiners');
        Schema::dropIfExists('seminars');
    }
}
