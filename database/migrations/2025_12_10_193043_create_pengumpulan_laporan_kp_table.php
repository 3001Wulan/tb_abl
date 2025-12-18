<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('pengumpulan_laporan_kp', function (Blueprint $table) {
        $table->id();
        $table->string('mahasiswa_id');
        $table->string('file_laporan');   // path file
        $table->boolean('is_format_valid')->default(false);
        $table->integer('plagiarism_score')->nullable();  // persentase plagiasi 0–100
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_laporan_kp');
    }
};
