<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PenilaianKP",
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            example: 1
        ),
        new OA\Property(
            property: "mahasiswa_id",
            type: "integer",
            example: 1
        ),
        new OA\Property(
            property: "nilai_laporan",
            type: "integer",
            example: 85,
            description: "Nilai laporan (0-100)"
        ),
        new OA\Property(
            property: "nilai_presentasi",
            type: "integer",
            example: 80,
            description: "Nilai presentasi (0-100)"
        ),
        new OA\Property(
            property: "nilai_aktivitas_kp",
            type: "integer",
            example: 90,
            description: "Nilai aktivitas (0-100)"
        ),
        new OA\Property(
            property: "nilai_akhir",
            type: "integer",
            example: 86,
            description: "Nilai akhir hasil perhitungan dengan bobot"
        ),
        new OA\Property(
            property: "nilai_mutu",
            type: "string",
            enum: ["A", "A-", "B+", "B", "B-", "C+", "C", "D", "E"],
            example: "A",
            description: "Konversi nilai akhir ke nilai mutu"
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
class PenilaianKPResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'mahasiswa_id' => $this->mahasiswa_id,
            'nilai_laporan' => $this->nilai_laporan,
            'nilai_presentasi' => $this->nilai_presentasi,
            'nilai_aktivitas_kp' => $this->nilai_aktivitas_kp,
            'nilai_akhir' => $this->nilai_akhir,
            'nilai_mutu' => $this->nilai_mutu,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}