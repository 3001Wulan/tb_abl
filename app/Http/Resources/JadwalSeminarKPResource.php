<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "JadwalSeminarKP",
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            example: 1
        ),
        new OA\Property(
            property: "title",
            type: "string",
            example: "Seminar KP - Sistem Informasi Akademik"
        ),
        new OA\Property(
            property: "student",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "name", type: "string", example: "John Doe"),
                new OA\Property(property: "email", type: "string", example: "john@example.com")
            ],
            type: "object"
        ),
        new OA\Property(
            property: "scheduled_at",
            type: "string",
            format: "date-time",
            nullable: true,
            example: "2024-12-25 10:00:00"
        ),
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["pending", "scheduled", "done", "cancelled"],
            example: "pending"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            nullable: true,
            example: "Catatan tambahan"
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            example: "2024-12-20 08:00:00"
        ),
        new OA\Property(
            property: "updated_at",
            type: "string",
            format: "date-time",
            example: "2024-12-20 08:00:00"
        )
    ]
)]
class JadwalSeminarKPResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title'=> $this->title,
            'student' => [
                'id' => $this->student?->id,
                'name' => $this->student?->name,
                'email' => $this->student?->email,
            ],
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}