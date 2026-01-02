<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanBimbingan extends Model {
    use HasFactory;
    protected $fillable = ['student_id', 'lecturer_id', 'tanggal', 'isi_catatan'];
    
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
