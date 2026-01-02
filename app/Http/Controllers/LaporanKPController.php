<?php

namespace App\Http\Controllers;

use App\Models\LaporanKP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Laporan KP",
    description: "API untuk manajemen laporan Kerja Praktik"
)]
class LaporanKPController extends Controller
{
    private function validateLaporanFormat($file)
    {
        if ($file->getClientOriginalExtension() !== 'pdf') {
            return false;
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return false;
        }

        return true;
    }

    #[OA\Post(
        path: "/api/laporan-kp/upload",
        summary: "Upload laporan KP",
        description: "Upload file laporan KP dalam format PDF (maksimal 10MB)",
        tags: ["Laporan KP"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["mahasiswa_id", "file_laporan"],
                    properties: [
                        new OA\Property(
                            property: "mahasiswa_id",
                            type: "integer",
                            description: "ID mahasiswa",
                            example: 1
                        ),
                        new OA\Property(
                            property: "file_laporan",
                            type: "string",
                            format: "binary",
                            description: "File laporan dalam format PDF (max 10MB)"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Laporan berhasil diupload",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Laporan berhasil diupload"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "mahasiswa_id", type: "integer", example: 1),
                                new OA\Property(property: "file_laporan", type: "string", example: "laporan_kp/abc123.pdf"),
                                new OA\Property(property: "is_format_valid", type: "boolean", example: true),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The file laporan field is required."),
                        new OA\Property(
                            property: "errors",
                            properties: [
                                new OA\Property(
                                    property: "file_laporan",
                                    type: "array",
                                    items: new OA\Items(type: "string", example: "The file laporan must be a file of type: pdf.")
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            )
        ]
    )]
    public function upload(Request $request)
{
    $request->validate([
        'mahasiswa_id' => 'required',
        'file_laporan' => 'required|file|mimes:pdf|max:10240'
    ]);

    $file = $request->file('file_laporan');

    $formatValid = $this->validateLaporanFormat($file);
    $originalFilename = $file->getClientOriginalName();
    
    $path = $file->store('laporan_kp', 'public');

    $data = LaporanKP::create([
        'mahasiswa_id'       => $request->mahasiswa_id,
        'file_laporan'       => $path,
        'original_filename'  => $originalFilename,  
        'is_format_valid'    => $formatValid
    ]);

    return response()->json([
        'message' => 'Laporan berhasil diupload',
        'data'    => $data
    ], 201);
}

    #[OA\Get(
        path: "/api/laporan-kp",
        summary: "List semua laporan KP",
        description: "Mengambil daftar semua laporan KP dengan optional filter",
        tags: ["Laporan KP"],
        parameters: [
            new OA\Parameter(
                name: "format_valid",
                in: "query",
                description: "Filter berdasarkan validitas format (1 = valid, 0 = tidak valid)",
                required: false,
                schema: new OA\Schema(type: "integer", enum: [0, 1])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List laporan berhasil diambil",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "mahasiswa_id", type: "integer", example: 1),
                            new OA\Property(property: "is_format_valid", type: "boolean", example: true),
                            new OA\Property(property: "created_at", type: "string", format: "date-time"),
                            new OA\Property(property: "updated_at", type: "string", format: "date-time")
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Tidak ada data ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Tidak ada data ditemukan"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items())
                    ]
                )
            )
        ]
    )]
   public function index(Request $request)
{
    $query = LaporanKP::query();

    if ($request->has('format_valid')) {
        $query->where(
            'is_format_valid',
            $request->format_valid == 1
        );
    }

    $data = $query->get([
        'id',
        'mahasiswa_id',
        'file_laporan',      
        'original_filename',
        'is_format_valid',
        'created_at',
        'updated_at'
    ]);

    return response()->json([
        'success' => true,
        'data' => $data
    ], 200);
}

    #[OA\Get(
        path: "/api/laporan-kp/{id}",
        summary: "Detail laporan KP",
        description: "Mengambil detail satu laporan KP berdasarkan ID",
        tags: ["Laporan KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID laporan KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detail laporan berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "mahasiswa_id", type: "integer", example: 1),
                        new OA\Property(property: "file_laporan", type: "string", example: "laporan_kp/abc123.pdf"),
                        new OA\Property(property: "is_format_valid", type: "boolean", example: true),
                        new OA\Property(property: "created_at", type: "string", format: "date-time"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Data tidak ditemukan")
                    ]
                )
            )
        ]
    )]
    public function show($id)
    {
        $data = LaporanKP::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($data);
    }

    #[OA\Post(
        path: "/api/laporan-kp/{id}",
        summary: "Update laporan KP",
        description: "Update/replace file laporan KP yang sudah ada",
        tags: ["Laporan KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID laporan KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file_laporan"],
                    properties: [
                        new OA\Property(
                            property: "file_laporan",
                            type: "string",
                            format: "binary",
                            description: "File laporan baru dalam format PDF (max 10MB)"
                        ),
                        new OA\Property(
                            property: "_method",
                            type: "string",
                            example: "PUT",
                            description: "Method spoofing untuk PUT request"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan berhasil diperbarui",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "File laporan berhasil diperbarui"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "mahasiswa_id", type: "integer", example: 1),
                                new OA\Property(property: "file_laporan", type: "string", example: "laporan_kp/xyz456.pdf"),
                                new OA\Property(property: "is_format_valid", type: "boolean", example: true),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Data tidak ditemukan")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            )
        ]
    )]
    public function update(Request $request, $id)
{
    $data = LaporanKP::find($id);

    if (!$data) {
        return response()->json([
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'file_laporan' => 'required|file|mimes:pdf|max:10240'
    ]);

    $file = $request->file('file_laporan');

    $formatValid = $this->validateLaporanFormat($file);

    if ($data->file_laporan && Storage::disk('public')->exists($data->file_laporan)) {
        Storage::disk('public')->delete($data->file_laporan);
    }

    $originalFilename = $file->getClientOriginalName();
    
    $path = $file->store('laporan_kp', 'public');

    $data->update([
        'file_laporan'       => $path,
        'original_filename'  => $originalFilename,  
        'is_format_valid'    => $formatValid
    ]);

    return response()->json([
        'message' => 'File laporan berhasil diperbarui',
        'data'    => $data
    ]);
}

    #[OA\Delete(
        path: "/api/laporan-kp/{id}",
        summary: "Hapus laporan KP",
        description: "Menghapus laporan KP beserta filenya",
        tags: ["Laporan KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID laporan KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan berhasil dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Data berhasil dihapus")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Data tidak ditemukan")
                    ]
                )
            )
        ]
    )]
    public function destroy($id)
    {
        $data = LaporanKP::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if ($data->file_laporan && Storage::disk('public')->exists($data->file_laporan)) {
            Storage::disk('public')->delete($data->file_laporan);
        }

        $data->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function showUploadPage()
{
    return view('mahasiswa.laporan-akhir'); 
}

public function showUploadForm()
{
    return view('mahasiswa.laporan-upload'); 
}

}