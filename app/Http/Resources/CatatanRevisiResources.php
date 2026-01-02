<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatatanRevisiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tipe' => $this->tipe,
            'deskripsi' => $this->deskripsi,
            'is_done' => $this->is_done,
            'created_at' => $this->created_at->format('d M Y H:i'),
            'updated_at' => $this->updated_at->format('d M Y H:i'),
        ];
    }
}