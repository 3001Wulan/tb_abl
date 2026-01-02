<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidasiLogbook extends Model
{
    use HasFactory;
    protected $table = 'validasi_logbooks'; 

    protected $fillable = [
        'logbook_kp_id', 
        'lecturer_id', 
        'status_validasi', 
        'catatan_pembimbing'
    ];

    public function logbook()
    {
        return $this->belongsTo(LogbookKP::class, 'logbook_kp_id');
    }
}