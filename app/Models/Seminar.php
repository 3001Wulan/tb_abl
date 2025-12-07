<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seminar extends Model
{
    protected $fillable = [
        'title','student_id','scheduled_at','status','notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class,'student_id');
    }

    public function examiners(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,      
            'seminar_examiners',        
            'seminar_id',               
            'examiner_id'              
        )
        ->withPivot('role')
        ->withTimestamps();
    }
}