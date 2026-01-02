<?php

namespace App\Http\Controllers;

use App\Models\InformasiKP;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="InformasiKP",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="jenis_informasi", type="string", example="syarat"),
 *     @OA\Property(property="judul", type="string", example="Syarat KP 2026"),
 *     @OA\Property(property="deskripsi", type="string", nullable=true, example="Deskripsi tambahan"),
 *     @OA\Property(property="is_wajib", type="boolean", nullable=true, example=true),
 *     @OA\Property(property="periode", type="string", nullable=true, example="Semester 2 2026"),
 *     @OA\Property(property="tanggal_mulai", type="string", format="date", nullable=true, example="2026-01-10"),
 *     @OA\Property(property="tanggal_selesai", type="string", format="date", nullable=true, example="2026-02-10"),
 *     @OA\Property(property="urutan", type="integer", nullable=true, example=1),
 *     @OA\Property(property="konten", type="string", nullable=true, example="Isi prosedur"),
 *     @OA\Property(property="jenis_dokumen", type="string", nullable=true, example="PDF"),
 *     @OA\Property(property="file_path", type="string", nullable=true, example="/files/template-laporan.pdf"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-19T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-19T12:00:00Z")
 * )
 */
class InformasiKPController extends Controller
{
    // ==================== CREATE / POST ====================
    /**
     * @OA\Post(
     *     path="/api/informasi-kp",
     *     summary="Tambah Informasi KP",
     *     tags={"Informasi KP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"jenis_informasi","judul"},
     *             @OA\Property(property="jenis_informasi", type="string", example="syarat"),
     *             @OA\Property(property="judul", type="string", example="Syarat KP 2026"),
     *             @OA\Property(property="deskripsi", type="string", example="Deskripsi tambahan"),
     *             @OA\Property(property="is_wajib", type="boolean", example=true),
     *             @OA\Property(property="periode", type="string", example="Semester 2 2026"),
     *             @OA\Property(property="tanggal_mulai", type="string", format="date", example="2026-01-10"),
     *             @OA\Property(property="tanggal_selesai", type="string", format="date", example="2026-02-10"),
     *             @OA\Property(property="urutan", type="integer", example=1),
     *             @OA\Property(property="konten", type="string", example="Isi prosedur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Informasi KP berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Informasi KP berhasil ditambahkan"),
     *             @OA\Property(property="data", ref="#/components/schemas/InformasiKP")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'deskripsi' => 'nullable|string',
            'is_wajib' => 'nullable|boolean',
            'periode' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'urutan' => 'nullable|integer'
        ]);

        $informasi = InformasiKP::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Informasi KP berhasil ditambahkan',
            'data' => $informasi
        ], 201);
    }

    // ==================== READ / GET ====================
    /**
     * @OA\Get(
     *     path="/api/informasi-kp",
     *     summary="Ambil semua Informasi KP",
     *     tags={"Informasi KP"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar Informasi KP",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/InformasiKP"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = InformasiKP::all();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/informasi-kp/{id}",
     *     summary="Ambil Informasi KP berdasarkan ID",
     *     tags={"Informasi KP"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informasi KP ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/InformasiKP")
     *     ),
     *     @OA\Response(response=404, description="Informasi KP tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $informasi = InformasiKP::find($id);
        if (!$informasi) {
            return response()->json(['status' => 'error', 'message' => 'Informasi KP tidak ditemukan'], 404);
        }
        return response()->json($informasi, 200);
    }

    // ==================== UPDATE / PUT ====================
    /**
     * @OA\Put(
     *     path="/api/informasi-kp/{id}",
     *     summary="Update Informasi KP",
     *     tags={"Informasi KP"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="jenis_informasi", type="string", example="syarat"),
     *             @OA\Property(property="judul", type="string", example="Syarat KP terbaru"),
     *             @OA\Property(property="deskripsi", type="string", example="Deskripsi terbaru"),
     *             @OA\Property(property="is_wajib", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informasi KP berhasil diupdate",
     *         @OA\JsonContent(ref="#/components/schemas/InformasiKP")
     *     ),
     *     @OA\Response(response=404, description="Informasi KP tidak ditemukan")
     * )
     */
    public function update(Request $request, $id)
    {
        $informasi = InformasiKP::find($id);
        if (!$informasi) {
            return response()->json(['status' => 'error', 'message' => 'Informasi KP tidak ditemukan'], 404);
        }

        $informasi->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Informasi KP berhasil diupdate',
            'data' => $informasi
        ], 200);
    }

    // ==================== DELETE / DELETE ====================
    /**
     * @OA\Delete(
     *     path="/api/informasi-kp/{id}",
     *     summary="Hapus Informasi KP",
     *     tags={"Informasi KP"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informasi KP berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Informasi KP berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Informasi KP tidak ditemukan")
     * )
     */
    public function destroy($id)
    {
        $informasi = InformasiKP::find($id);
        if (!$informasi) {
            return response()->json(['status' => 'error', 'message' => 'Informasi KP tidak ditemukan'], 404);
        }

        $informasi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Informasi KP berhasil dihapus'
        ], 200);
    }
}
