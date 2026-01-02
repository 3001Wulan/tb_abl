<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatRevisiLaporan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_revisi_laporan';

    protected $fillable = [
        'laporan_kp_id',
        'versi',
        'file_path',
        'keterangan',
        'is_format_valid',
        'is_final',
    ];

    protected $casts = [
        'is_format_valid' => 'boolean',
        'is_final' => 'boolean',
    ];

    /**
     * Relasi ke LaporanKP
     */
    public function laporanKp()
    {
        return $this->belongsTo(LaporanKP::class, 'laporan_kp_id');
    }
}