<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranKP extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_kp';

    protected $fillable = [
        'student_id',
        'judul_kp',
        'lokasi',
        'periode',
        'status',
        'berkas',
        'krs',
        'transkrip',
        'proposal'
    ];
}
