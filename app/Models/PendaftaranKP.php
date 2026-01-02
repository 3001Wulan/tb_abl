<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranKP extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_kp';
    
    protected $fillable = [
        'nama_mahasiswa',
        'nim',
        'email',
        'no_hp',
        'jurusan',
        'universitas',
        'tema_kp',
        'perusahaan',
        'krs',
        'transkrip',
        'proposal',
        'status_validasi',
        'catatan_validasi'
    ];

    protected $dates = ['created_at', 'updated_at'];

    // Cast untuk tipe data
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Accessor untuk path file lengkap
    public function getKrsUrlAttribute()
    {
        return $this->krs ? asset('storage/' . $this->krs) : null;
    }

    public function getTranskripUrlAttribute()
    {
        return $this->transkrip ? asset('storage/' . $this->transkrip) : null;
    }

    public function getProposalUrlAttribute()
    {
        return $this->proposal ? asset('storage/' . $this->proposal) : null;
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }
}
