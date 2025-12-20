<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->string('original_filename')->nullable()->after('file_laporan');
        });
    }

    public function down()
    {
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->dropColumn('original_filename');
        });
    }
};