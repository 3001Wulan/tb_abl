<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InformasiKP extends Model
{
    use HasFactory;

    protected $table = 'informasi_kp';

    protected $fillable = [
        'tipe',         
        'judul',
        'deskripsi',
        'is_wajib',          
        'tahun_akademik',    
        'periode',           
        'tanggal_mulai',     
        'tanggal_selesai',   
        'jenis_dokumen',     
        'file_path',         
        'urutan',            
        'estimasi_waktu'     
    ];

    protected $casts = [
        'is_wajib' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'urutan' => 'integer'
    ];


    public function scopeSyarat($query)
    {
        return $query->where('tipe', 'syarat');
    }

    public function scopeJadwal($query)
    {
        return $query->where('tipe', 'jadwal');
    }

    public function scopeTemplate($query)
    {
        return $query->where('tipe', 'template');
    }

    public function scopeProsedur($query)
    {
        return $query->where('tipe', 'prosedur');
    }
}