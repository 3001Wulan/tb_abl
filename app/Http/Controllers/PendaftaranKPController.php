<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\PendaftaranKP;

class PendaftaranKPController extends Controller
{
    /**
 * @OA\Post(
 *     path="/pendaftaran-kp",
 *     tags={"Pendaftaran KP"},
 *     summary="Daftar Kerja Praktik",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"student_id","judul_kp","lokasi","periode"},
 *                 @OA\Property(property="student_id", type="integer", example=1),
 *                 @OA\Property(property="judul_kp", type="string", example="KP Diskominfo"),
 *                 @OA\Property(property="lokasi", type="string", example="Padang"),
 *                 @OA\Property(property="periode", type="string", example="2026"),
 *                 @OA\Property(property="proposal", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Pendaftaran KP berhasil"),
 *     @OA\Response(response=422, description="Validasi gagal")
 * )
 */

    public function daftar(Request $request)
{
    $validator = Validator::make($request->all(), [
        'student_id' => 'required|integer',
        'judul_kp'   => 'required|string',
        'lokasi'     => 'required|string',
        'periode'    => 'required|string',
        'proposal'   => 'nullable|file|mimes:pdf,doc,docx|max:5120',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    $proposalPath = null;

    if ($request->hasFile('proposal')) {
        $proposalPath = $request->file('proposal')
                                ->store('proposal_kp', 'public');
    }

    $data = PendaftaranKP::create([
        'student_id' => $request->student_id,
        'judul_kp'   => $request->judul_kp,
        'lokasi'     => $request->lokasi,
        'periode'    => $request->periode,
        'proposal'   => $proposalPath,
        'status'     => 'pending'
    ]);

    return response()->json([
        'message' => 'Pendaftaran KP berhasil',
        'data'    => $data
    ], 201);
}

/**
 * Ambil semua pendaftaran KP milik mahasiswa tertentu
 *
 * @OA\Get(
 *     path="/pendaftaran-kp/saya/{studentId}",
 *     tags={"Pendaftaran KP"},
 *     summary="Ambil semua pendaftaran KP milik mahasiswa tertentu",
 *     @OA\Parameter(
 *         name="studentId",
 *         in="path",
 *         required=true,
 *         description="ID mahasiswa",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil pendaftaran mahasiswa",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="student_id", type="integer", example=5),
 *                     @OA\Property(property="judul_kp", type="string", example="KP Diskominfo"),
 *                     @OA\Property(property="lokasi", type="string", example="Padang"),
 *                     @OA\Property(property="periode", type="string", example="2026"),
 *                     @OA\Property(property="proposal", type="string", nullable=true, example="/storage/proposal_kp/file.pdf"),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-22T08:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-22T08:00:00Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */

    public function daftarSaya($studentId)
    {
        $pendaftaran = PendaftaranKP::where('student_id', $studentId)->get();

        if ($pendaftaran->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], 404);
        }

        $data = $pendaftaran->map(function($item) {
            $item->proposal = $item->proposal ? asset('storage/' . $item->proposal) : null;
            $item->created_at = $item->created_at->toIso8601String();
            $item->updated_at = $item->updated_at->toIso8601String();
            return $item;
        });

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/pendaftaran-kp/{id}",
     *     tags={"Pendaftaran KP"},
     *     summary="Detail pendaftaran KP",
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Data tidak ditemukan")
     * )
     */
    public function detail($id)
    {
        $data = PendaftaranKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['data' => $data], 200);
    }
/**
 * @OA\Post(
 *     path="/pendaftaran-kp/{id}",
 *     operationId="updatePendaftaranKP",
 *     tags={"Pendaftaran KP"},
 *     summary="Update pendaftaran KP + upload proposal",
 *     description="Endpoint menggunakan POST + _method=PUT untuk kompatibilitas browser. Di API client bisa langsung PUT.",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID pendaftaran KP",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"_method"},
 *                 @OA\Property(
 *                     property="_method",
 *                     type="string",
 *                     example="PUT",
 *                     description="Method spoofing untuk Laravel agar POST dianggap PUT"
 *                 ),
 *                 @OA\Property(
 *                     property="judul_kp",
 *                     type="string",
 *                     example="KP Diskominfo",
 *                     description="Judul Kerja Praktik"
 *                 ),
 *                 @OA\Property(
 *                     property="lokasi",
 *                     type="string",
 *                     example="Padang",
 *                     description="Lokasi Kerja Praktik"
 *                 ),
 *                 @OA\Property(
 *                     property="periode",
 *                     type="string",
 *                     example="2026",
 *                     description="Periode KP"
 *                 ),
 *                 @OA\Property(
 *                     property="status",
 *                     type="string",
 *                     example="pending",
 *                     description="Status pendaftaran KP"
 *                 ),
 *                 @OA\Property(
 *                     property="proposal",
 *                     type="string",
 *                     format="binary",
 *                     description="File proposal (pdf, doc, docx) - opsional"
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Data berhasil diperbarui",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Data berhasil diperbarui"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Data tidak ditemukan",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Data tidak ditemukan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validasi gagal",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Validasi gagal"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */



public function update(Request $request, $id)
{
    $data = PendaftaranKP::findOrFail($id);

    $validated = $request->validate([
        'judul_kp' => 'nullable|string',
        'lokasi'   => 'nullable|string',
        'periode'  => 'nullable|string',
        'status'   => 'nullable|string',
        'proposal' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
    ]);

    $data->fill($request->only([
        'judul_kp',
        'lokasi',
        'periode',
        'status'
    ]));

    if ($request->hasFile('proposal')) {
        if ($data->proposal && \Storage::disk('public')->exists($data->proposal)) {
            \Storage::disk('public')->delete($data->proposal);
        }

        $data->proposal = $request->file('proposal')->store('proposal_kp', 'public');
    }

    $data->save();

    return response()->json([
        'message' => 'Data berhasil diperbarui',
        'data' => $data
    ], 200);
}
public function delete($id)
    {
        try {
            $pendaftaran = PendaftaranKP::findOrFail($id);
            $pendaftaran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
