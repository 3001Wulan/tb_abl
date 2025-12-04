<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPengantarKP extends Model
{
    protected $table = 'surat_pengantar_kp';
    
    protected $fillable = [
        'pendaftaran_kp_id',
        'nomor_surat',
        'nama_mahasiswa',
        'nim',
        'jurusan',
        'universitas',
        'nama_perusahaan',
        'alamat_perusahaan',
        'kontak_perusahaan',
        'nama_pembimbing_akademik',
        'nip_pembimbing',
        'status_pengajuan',
        'status_penandatanganan',
        'file_path',
        'nama_file_pdf',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'tanggal_penandatanganan',
        'catatan_pengajuan',
        'alasan_penolakan',
        'tanggal_pengajuan',
        'tanggal_selesai'
    ];

    protected $dates = ['tanggal_pengajuan', 'tanggal_penandatanganan', 'tanggal_selesai'];

    // Relasi ke Pendaftaran KP
    public function pendaftaranKP()
    {
        return $this->belongsTo(PendaftaranKP::class, 'pendaftaran_kp_id');
    }
}