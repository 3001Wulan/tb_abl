<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Student extends Model

{
    public $incrementing = false; 
    use HasFactory;

    protected $fillable = ['nim', 'nama', 'email'];

public function student()
{
    return $this->hasOne(Student::class, 'id', 'id');
}
}
