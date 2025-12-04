<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Student extends Model
{
    use HasFactory;

    protected $fillable = ['nim', 'nama', 'email'];

    // Jika tabel bukan 'students', tambahkan property $table
    // protected $table = 'mahasiswa';
}
