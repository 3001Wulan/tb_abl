<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKP extends Model
{
    protected $table = 'laporan_kp';

    protected $fillable = [
        'mahasiswa_id',
        'file_laporan',
        'original_filename',
        'is_format_valid',
    ];
}
