<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreJadwalSeminarKPRequest",
    required: ["title", "student_id", "status"],
    properties: [
        new OA\Property(
            property: "title",
            type: "string",
            maxLength: 255,
            example: "Seminar KP - Sistem Informasi Akademik"
        ),
        new OA\Property(
            property: "student_id",
            type: "integer",
            example: 1
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
            example: "Catatan tambahan untuk seminar"
        )
    ]
)]
class StoreJadwalSeminarKPRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'student_id' => 'required|exists:users,id',
            'scheduled_at' => 'nullable|date',
            'status' => 'required|in:pending,scheduled,done,cancelled',
            'notes' => 'nullable|string',
        ];
    }
}