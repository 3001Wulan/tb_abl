<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalBimbingan extends Model {
    use HasFactory;
    protected $fillable = ['student_id', 'lecturer_id', 'tanggal', 'waktu', 'status'];
}
