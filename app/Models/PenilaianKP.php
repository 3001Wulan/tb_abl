<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianKP extends Model
{
    protected $table = 'penilaian_kp';

    protected $fillable = [
        'mahasiswa_id',
        'nilai_laporan',
        'nilai_presentasi',
        'nilai_aktivitas_kp',
        'nilai_akhir',
        'nilai_mutu'
    ];
}
