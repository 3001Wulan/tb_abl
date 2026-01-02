<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiAdministrasi extends Model
{
    protected $table = 'verifikasi_administrasi';
    
    protected $fillable = [
        'pendaftaran_kp_id',
        'nomor_verifikasi',
        'nama_mahasiswa',
        'nim',
        'jurusan',
        'krs_lengkap',
        'transkrip_lengkap',
        'proposal_lengkap',
        'catatan_kelengkapan',
        'status_verifikasi',
        'status_persetujuan',
        'alasan_tolak',
        'nama_pemeriksa',
        'tanggal_verifikasi',
        'tanggal_persetujuan',
        'status_mahasiswa',
        'pesan_ke_mahasiswa'
    ];

    protected $dates = ['tanggal_verifikasi', 'tanggal_persetujuan'];
    
    protected $casts = [
        'krs_lengkap' => 'boolean',
        'transkrip_lengkap' => 'boolean',
        'proposal_lengkap' => 'boolean'
    ];

    public function pendaftaranKP()
    {
        return $this->belongsTo(PendaftaranKP::class, 'pendaftaran_kp_id');
    }
}