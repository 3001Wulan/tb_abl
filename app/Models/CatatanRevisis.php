<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanRevisi extends Model
{
    use HasFactory;

    protected $table = 'catatan_revisi';

    protected $fillable = [
        'administrasi_seminar_id',
        'tipe',
        'deskripsi',
        'is_done',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    /**
     * Relasi ke AdministrasiSeminar
     */
    public function administrasiSeminar()
    {
        return $this->belongsTo(AdministrasiSeminar::class, 'administrasi_seminar_id');
    }
}