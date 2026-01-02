<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKP extends Model
{
    use HasFactory;

    protected $table = 'laporan_kp';

    protected $fillable = [
        'mahasiswa_id',
        'file_laporan', // bisa dihapus atau dijadikan opsional karena sekarang pakai riwayat_revisi_laporan
        'is_format_valid',
        'status', // draft, submitted, approved, rejected
    ];

    protected $casts = [
        'is_format_valid' => 'boolean',
    ];

    /**
     * Relasi ke User (mahasiswa)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke RiwayatRevisiLaporan
     */
    public function riwayatRevisi()
    {
        return $this->hasMany(RiwayatRevisiLaporan::class, 'laporan_kp_id');
    }

    /**
     * Get versi final
     */
    public function versiFinal()
    {
        return $this->hasOne(RiwayatRevisiLaporan::class, 'laporan_kp_id')
            ->where('is_final', true);
    }

    /**
     * Get versi terbaru
     */
    public function versiTerbaru()
    {
        return $this->hasOne(RiwayatRevisiLaporan::class, 'laporan_kp_id')
            ->orderBy('versi', 'desc');
    }
}