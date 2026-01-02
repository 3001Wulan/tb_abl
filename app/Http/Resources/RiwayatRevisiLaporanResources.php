<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RiwayatRevisiLaporanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'laporan_kp_id' => $this->laporan_kp_id,
            'versi' => $this->versi,
            'file_path' => $this->file_path,
            'file_url' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'file_size' => Storage::disk('public')->exists($this->file_path) 
                ? round(Storage::disk('public')->size($this->file_path) / 1024 / 1024, 2) . ' MB'
                : null,
            'keterangan' => $this->keterangan,
            'is_format_valid' => $this->is_format_valid,
            'is_final' => $this->is_final,
            'tanggal_upload' => $this->created_at->format('d M Y H:i'),
            'tanggal_upload_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}