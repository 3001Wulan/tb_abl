<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StorePenilaianKPRequest",
    required: ["mahasiswa_id", "nilai_laporan", "nilai_presentasi", "nilai_aktivitas_kp"],
    properties: [
        new OA\Property(
            property: "mahasiswa_id",
            type: "integer",
            example: 1,
            description: "ID mahasiswa"
        ),
        new OA\Property(
            property: "nilai_laporan",
            type: "integer",
            minimum: 0,
            maximum: 100,
            example: 85,
            description: "Nilai laporan KP (0-100)"
        ),
        new OA\Property(
            property: "nilai_presentasi",
            type: "integer",
            minimum: 0,
            maximum: 100,
            example: 80,
            description: "Nilai presentasi KP (0-100)"
        ),
        new OA\Property(
            property: "nilai_aktivitas_kp",
            type: "integer",
            minimum: 0,
            maximum: 100,
            example: 90,
            description: "Nilai aktivitas KP (0-100)"
        ),
        new OA\Property(
    property: "opsi_perhitungan",
    type: "string",
    enum: ["1", "2"],
    description: "Opsi perhitungan: 1 (laporan 50%, presentasi 20%, aktivitas 30%) atau 2 (laporan 40%, presentasi 30%, aktivitas 30%)"
)
    ]
)]
class StorePenilaianKPRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    return [
        'mahasiswa_id'        => 'required|unique:penilaian_kp,mahasiswa_id',
        'nilai_laporan'       => 'required|integer|min:0|max:100',
        'nilai_presentasi'    => 'required|integer|min:0|max:100',
        'nilai_aktivitas_kp'  => 'required|integer|min:0|max:100',
        'opsi_perhitungan'    => 'nullable|string|in:1,2'
    ];
}

public function messages()
{
    return [
        'mahasiswa_id.unique' => 'Mahasiswa ini sudah memiliki penilaian. Silakan gunakan update untuk mengubah nilai.'
    ];
}
}