<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('penilaian_kp', function (Blueprint $table) {
        $table->string('nilai_mutu')->nullable();
    });
}

public function down(): void
{
    Schema::table('penilaian_kp', function (Blueprint $table) {
        $table->dropColumn('nilai_mutu');
    });
}

};
