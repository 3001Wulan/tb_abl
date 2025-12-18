<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumpulanLaporanKP extends Model
{
    protected $table = 'pengumpulan_laporan_kp';

    protected $fillable = [
        'mahasiswa_id',
        'file_laporan',
        'is_format_valid',
        'plagiarism_score',
    ];
}
