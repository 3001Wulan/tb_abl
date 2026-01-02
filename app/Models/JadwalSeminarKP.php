<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalSeminarKp extends Model
{
    protected $table = 'jadwal_seminar_kp';

    protected $fillable = [
        'title',
        'student_id',
        'scheduled_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
