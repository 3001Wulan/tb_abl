<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeminarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title'=> $this->title,
            'student' => [
                'id' => $this->student->id ?? null,
                'name' => $this->student->name ?? null,
                'email' => $this->student->email ?? null,
            ],
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
            'notes' => $this->notes,
            'examiners' => $this->examiners->map(function($e){
                return [
                    'id' => $e->id,
                    'name' => $e->name,
                    'email' => $e->email,
                    'role' => $e->pivot->role ?? null,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
