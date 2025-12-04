<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarForum extends Model {
    use HasFactory;
    protected $fillable = ['forum_id', 'student_id', 'konten'];
}
