<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookKP extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'minggu_ke', 
        'tanggal_mulai', 
        'deskripsi_kegiatan', 
        'status' // Disetujui, Ditolak, Pending
    ];

    // Relasi: Satu Logbook memiliki satu Validasi
    public function validasi()
    {
        return $this->hasOne(ValidasiLogbook::class, 'logbook_kp_id');
    }
    public function student()
{
    return $this->belongsTo(Student::class, 'student_id');
}

}