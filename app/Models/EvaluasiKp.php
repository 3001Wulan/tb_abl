<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiKP extends Model
{
    protected $table = 'evaluasi_kp';

    protected $fillable = [
        'logbook_kp_id',
        'lecturer_id',
        'progres_pencapaian',
        'penilaian_sementara',
        'catatan_pembimbing',
        'rekomendasi_lanjutan'
    ];

    public function logbook()
    {
        return $this->belongsTo(LogbookKP::class, 'logbook_kp_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }
}
