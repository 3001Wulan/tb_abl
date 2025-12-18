<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogbookKP;
use App\Models\ValidasiLogbook;
use Illuminate\Support\Facades\Storage;
use App\Models\EvaluasiTengahKP;

/**
 * @OA\Tag(
 * name="Logbook (Siswa)",
 * description="Operasi CRUD dan Tracking Logbook kegiatan KP oleh Mahasiswa."
 * )
 * @OA\Schema(
 * schema="LogbookKP",
 * title="LogbookKP",
 * description="Model data Logbook Kerja Praktik.",
 * @OA\Property(property="id", type="integer", readOnly=true, example=1),
 * @OA\Property(property="student_id", type="integer", example=5),
 * @OA\Property(property="minggu_ke", type="integer", example=3, description="Minggu ke- KP (unik per siswa)."),
 * @OA\Property(property="tanggal_mulai", type="string", format="date", example="2025-01-20"),
 * @OA\Property(property="deskripsi_kegiatan", type="string", example="Melakukan instalasi dan konfigurasi server."),
 * @OA\Property(property="file_kegiatan", type="string", nullable=true, example="logbook_files/abc.pdf", description="Path file kegiatan."),
 * @OA\Property(property="status", type="string", enum={"Pending", "Validated", "Rejected"}, default="Pending", readOnly=true),
 * )
 * @OA\Schema(
 * schema="ValidasiLogbook",
 * title="ValidasiLogbook",
 * description="Model data Validasi Logbook oleh Dosen/Validator.",
 * @OA\Property(property="id", type="integer", readOnly=true, example=1),
 * @OA\Property(property="status", type="string", enum={"Validated", "Rejected"}, example="Validated"),
 * @OA\Property(property="catatan_validator", type="string", nullable=true, example="Laporan mingguan sudah baik."),
 * )
 * @OA\Schema(
 * schema="LogbookKPWithValidation",
 * allOf={
 * @OA\Schema(ref="#/components/schemas/LogbookKP"),
 * @OA\Schema(
 * @OA\Property(property="validasi", ref="#/components/schemas/ValidasiLogbook", nullable=true)
 * )
 * }
 * )
 */
class LogbookController extends Controller {

    // ==== Layanan 3: Logbook / Monitoring KP (CRUD & Tracking oleh Siswa) ====
    
    /**
     * @OA\Post(
     * path="/logbook",
     * operationId="storeLogbook",
     * tags={"Logbook (Siswa)"},
     * summary="Siswa mengunggah Logbook kegiatan mingguan (termasuk file).",
     * description="Mengunggah Logbook baru. Mendukung pengiriman file kegiatan opsional.",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"student_id", "minggu_ke", "tanggal_mulai", "deskripsi_kegiatan"},
     * @OA\Property(property="student_id", type="integer", example=5, description="ID Siswa"),
     * @OA\Property(property="minggu_ke", type="integer", example=3, description="Minggu ke- KP (unik per siswa)"),
     * @OA\Property(property="tanggal_mulai", type="string", format="date", example="2025-01-20", description="Tanggal mulai minggu kegiatan (YYYY-MM-DD)"),
     * @OA\Property(property="deskripsi_kegiatan", type="string", example="Melakukan instalasi dan konfigurasi server."),
     * @OA\Property(property="file_kegiatan", type="string", format="binary", nullable=true, description="File pendukung (maks 2MB, pdf, doc, docx, jpg, jpeg, png).")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Logbook berhasil dibuat.",
     * @OA\JsonContent(ref="#/components/schemas/LogbookKP")
     * ),
     * @OA\Response(response=422, description="Kesalahan Validasi.")
     * )
     */
    public function storeLogbook(Request $request) 
    {
        $studentId = auth()->id();
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'minggu_ke' => 'required|integer|unique:logbook_k_p_s,minggu_ke,NULL,id,student_id,' . $request->student_id,
            'tanggal_mulai' => 'required|date',
            'deskripsi_kegiatan' => 'required',
            'file_kegiatan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file_kegiatan')) {
            $filePath = $request->file('file_kegiatan')->store('logbook_files', 'public');
        }

        $logbook = LogbookKP::create([
            'student_id' => $request->student_id,
            'minggu_ke' => $request->minggu_ke,
            'tanggal_mulai' => $request->tanggal_mulai,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'file_kegiatan' => $filePath,
        ]);

        return response()->json($logbook, 201);
    }


    /**
     * @OA\Get(
     * path="/logbook/{studentId}",
     * operationId="indexLogbook",
     * tags={"Logbook (Siswa)"},
     * summary="Mengambil semua Logbook (beserta validasinya) untuk seorang siswa.",
     * description="Menampilkan daftar semua Logbook siswa untuk pelacakan kemajuan KP.",
     * @OA\Parameter(
     * name="studentId",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", format="int64", example=5),
     * description="ID Siswa"
     * ),
     * @OA\Response(
     * response=200,
     * description="Daftar Logbook siswa.",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/LogbookKPWithValidation")
     * )
     * )
     * )
     */
    public function indexLogbook($studentId) {
        return response()->json(LogbookKP::where('student_id', $studentId)->with('validasi')->get());
    }

    /**
     * @OA\Get(
     * path="/logbook/detail/{logbookId}",
     * operationId="showLogbook",
     * tags={"Logbook (Siswa)"},
     * summary="Menampilkan detail satu Logbook.",
     * description="Mendapatkan detail Logbook beserta data validasinya.",
     * @OA\Parameter(
     * name="logbookId",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", format="int64", example=10),
     * description="ID Logbook"
     * ),
     * @OA\Response(
     * response=200,
     * description="Detail Logbook.",
     * @OA\JsonContent(ref="#/components/schemas/LogbookKPWithValidation")
     * ),
     * @OA\Response(response=404, description="Logbook tidak ditemukan.")
     * )
     */
    public function showLogbook($logbookId) {
        $logbook = LogbookKP::with('validasi')->findOrFail($logbookId);
        return response()->json($logbook);
    }

    /**
 * @OA\Post(
 *     path="/logbook/{logbookId}",
 *     operationId="updateLogbook",
 *     tags={"Logbook (Siswa)"},
 *     summary="Siswa mengupdate Logbook yang belum divalidasi",
 *     description="Memperbarui data Logbook KP. Pembaruan hanya dapat dilakukan jika status Logbook masih 'Pending'. File kegiatan dapat diganti (opsional).",
 *
 *     @OA\Parameter(
 *         name="logbookId",
 *         in="path",
 *         required=true,
 *         description="ID Logbook",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"minggu_ke", "tanggal_mulai", "deskripsi_kegiatan"},
 *                 @OA\Property(
 *                     property="minggu_ke",
 *                     type="integer",
 *                     example=4,
 *                     description="Minggu ke kegiatan KP"
 *                 ),
 *                 @OA\Property(
 *                     property="tanggal_mulai",
 *                     type="string",
 *                     format="date",
 *                     example="2025-01-27"
 *                 ),
 *                 @OA\Property(
 *                     property="deskripsi_kegiatan",
 *                     type="string",
 *                     example="Melakukan coding modul A."
 *                 ),
 *                 @OA\Property(
 *                     property="file_kegiatan",
 *                     type="string",
 *                     format="binary",
 *                     description="File laporan/kegiatan (pdf, doc, docx, jpg, png) — opsional"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Logbook berhasil diperbarui",
 *         @OA\JsonContent(ref="#/components/schemas/LogbookKP")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Logbook sudah divalidasi dan tidak dapat diubah"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Logbook tidak ditemukan"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Kesalahan validasi"
 *     )
 * )
 */
    public function updateLogbook(Request $request, $logbookId)
    {
        $logbook = LogbookKP::findOrFail($logbookId);
    
        // Ini HANYA menolak jika statusnya sudah Disetujui
if ($logbook->status === 'Disetujui') {
    return response()->json([
        'message' => 'Logbook sudah disetujui dan tidak dapat diubah.'
    ], 403);
}
    
        // Validasi input
        $request->validate([
            'minggu_ke' => 'required|integer|unique:logbook_k_p_s,minggu_ke,' 
                . $logbookId . ',id,student_id,' . $logbook->student_id,
            'tanggal_mulai' => 'required|date',
            'deskripsi_kegiatan' => 'required|string',
            'file_kegiatan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);
    
        $data = [
            'minggu_ke' => $request->minggu_ke,
            'tanggal_mulai' => $request->tanggal_mulai,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
        ];
    
        if ($request->hasFile('file_kegiatan')) {
    
            if ($logbook->file_kegiatan && Storage::disk('public')->exists($logbook->file_kegiatan)) {
                Storage::disk('public')->delete($logbook->file_kegiatan);
            }

            $filePath = $request->file('file_kegiatan')
                ->store('logbook_files', 'public');
    
            $data['file_kegiatan'] = $filePath;
        }

        $logbook->update($data);
    
        return response()->json([
            'message' => 'Logbook berhasil diperbarui',
            'data' => $logbook
        ], 200);
    }
    
    /**
     * @OA\Delete(
     * path="/logbook/{logbookId}",
     * operationId="destroyLogbook",
     * tags={"Logbook (Siswa)"},
     * summary="Siswa menghapus Logbook yang belum divalidasi.",
     * description="Menghapus data Logbook. Penghapusan diblokir jika status Logbook bukan 'Pending'.",
     * @OA\Parameter(
     * name="logbookId",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer", format="int64", example=10),
     * description="ID Logbook"
     * ),
     * @OA\Response(
     * response=200,
     * description="Logbook berhasil dihapus.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Logbook berhasil dihapus.")
     * )
     * ),
     * @OA\Response(response=403, description="Logbook sudah divalidasi dan tidak dapat dihapus."),
     * @OA\Response(response=404, description="Logbook tidak ditemukan.")
     * )
     */
    public function destroyLogbook($logbookId) {
        $logbook = LogbookKP::findOrFail($logbookId);

        if ($logbook->status !== 'Pending') {
            return response()->json(['message' => 'Logbook sudah divalidasi dan tidak dapat dihapus.'], 403);
        }

        $logbook->delete();
        return response()->json(['message' => 'Logbook berhasil dihapus.'], 200);
    }

    public function create()
    {

        return view('mahasiswa.logbook_form');
    }

    public function edit($id)
    {

        return view('mahasiswa.logbook_edit', compact('id'));

    }
}