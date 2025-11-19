<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'keahlian',
        'kuota_bimbingan',
        'email',
    ];

    // Relasi ke supervisi (bimbingan)
    public function supervisions()
    {
        return $this->hasMany(Supervision::class);
    }
}
