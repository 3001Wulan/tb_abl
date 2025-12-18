<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanTempatKP extends Model
{
    protected $table = 'pengajuan_tempat_kp';

    protected $fillable = [
        'pendaftaran_kp_id',
        'nama_mahasiswa',
        'nim',
        'nama_instansi',
        'alamat_instansi',
        'kontak_instansi',
        'bidang_usaha',
        'catatan_pengajuan',
        'tanggal_pengajuan',
        'status_pengajuan',
        'status_persetujuan_instansi',
        'nama_pembimbing_instansi',
        'kontak_pembimbing',
        'catatan_instansi',
        'catatan_jurusan',
        'tanggal_persetujuan_instansi',
        'tanggal_persetujuan_jurusan'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_persetujuan_instansi' => 'date',
        'tanggal_persetujuan_jurusan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranKP::class, 'pendaftaran_kp_id');
    }
}