<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Supervision extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'lecturer_id', 'judul'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function lecturer() {
        return $this->belongsTo(Lecturer::class);
    }
}
