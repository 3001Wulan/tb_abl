<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    protected $fillable = [
        'supervision_id',
        'nomor_surat',
        'file_path'
    ];
}
