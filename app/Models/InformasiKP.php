<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InformasiKP extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'informasi_kp';

    // Kolom yang bisa diisi
    protected $fillable = [
        'tipe',              // syarat/jadwal/template/prosedur
        'judul',
        'deskripsi',
        'is_wajib',          // untuk syarat
        'tahun_akademik',    // untuk jadwal
        'periode',           // untuk jadwal
        'tanggal_mulai',     // untuk jadwal
        'tanggal_selesai',   // untuk jadwal
        'jenis_dokumen',     // untuk template
        'file_path',         // untuk template
        'urutan',            // untuk prosedur
        'estimasi_waktu'     // untuk prosedur
    ];

    // Casting tipe data
    protected $casts = [
        'is_wajib' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'urutan' => 'integer'
    ];

    // ============================================
    // SCOPE - Helper untuk filter data
    // ============================================
    
    // Ambil data syarat saja
    public function scopeSyarat($query)
    {
        return $query->where('tipe', 'syarat');
    }

    // Ambil data jadwal saja
    public function scopeJadwal($query)
    {
        return $query->where('tipe', 'jadwal');
    }

    // Ambil data template saja
    public function scopeTemplate($query)
    {
        return $query->where('tipe', 'template');
    }

    // Ambil data prosedur saja
    public function scopeProsedur($query)
    {
        return $query->where('tipe', 'prosedur');
    }
}