<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ValidasiLogbook;
use App\Models\LogbookKP;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Validasi Logbook",
 *     description="API untuk melakukan validasi logbook mahasiswa"
 * )
 */
class ValidasiController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/validasi",
     *     summary="Membuat validasi baru",
     *     tags={"Validasi Logbook"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"logbook_kp_id", "lecturer_id", "status_validasi"},
     *             @OA\Property(property="logbook_kp_id", type="integer", example=1),
     *             @OA\Property(property="lecturer_id", type="integer", example=12),
     *             @OA\Property(property="status_validasi", type="string", example="Disetujui"),
     *             @OA\Property(property="catatan_pembimbing", type="string", example="Bagus, lanjutkan.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Validasi berhasil dibuat"
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function storeValidasi(Request $request)
    {
        $request->validate([
            'logbook_kp_id' => 'required|exists:logbook_k_p_s,id',
            'lecturer_id' => 'required|exists:users,id',
            'status_validasi' => 'required|in:Disetujui,Revisi',
            'catatan_pembimbing' => 'nullable|string'
        ]);

        $validasi = ValidasiLogbook::create([
            'logbook_kp_id' => $request->logbook_kp_id,
            'lecturer_id' => $request->lecturer_id,
            'status_validasi' => $request->status_validasi,
            'catatan_pembimbing' => $request->catatan_pembimbing
        ]);

        $logbook = LogbookKP::find($request->logbook_kp_id);
        $logbook->status = $request->status_validasi;
        $logbook->save();

        return response()->json([
            'message' => 'Validasi berhasil dibuat',
            'data'    => $validasi
        ], 201);
    }


    /**
     * @OA\Get(
     *     path="/api/validasi",
     *     summary="Menampilkan semua data validasi",
     *     tags={"Validasi Logbook"},
     *
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function indexValidasi()
    {
        $data = ValidasiLogbook::with('logbook.student')->get();

        return response()->json([
            'message' => 'Daftar semua validasi logbook',
            'data'    => $data
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/validasi/{id}",
     *     summary="Menampilkan detail validasi berdasarkan ID",
     *     tags={"Validasi Logbook"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         required=true,
     *         in="path",
     *         description="ID validasi",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function showValidasi($id)
    {
        $data = ValidasiLogbook::with('logbook.student')->find($id);

        if (!$data) {
            return response()->json(['message' => 'Validasi tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail validasi',
            'data'    => $data
        ], 200);
    }


    /**
     * @OA\Put(
     *     path="/api/validasi/{id}",
     *     summary="Memperbarui validasi logbook",
     *     tags={"Validasi Logbook"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         required=true,
     *         in="path",
     *         description="ID validasi yang akan diperbarui",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="status_validasi", type="string", example="Revisi"),
     *             @OA\Property(property="catatan_pembimbing", type="string", example="Tolong revisi bagian hasil.")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function updateValidasi(Request $request, $id)
    {
        $validasi = ValidasiLogbook::find($id);

        if (!$validasi) {
            return response()->json(['message' => 'Validasi tidak ditemukan'], 404);
        }

        $request->validate([
            'status_validasi' => 'in:Disetujui,Revisi',
            'catatan_pembimbing' => 'nullable|string'
        ]);

        $validasi->update($request->only(['status_validasi', 'catatan_pembimbing']));

        return response()->json([
            'message' => 'Validasi berhasil diperbarui',
            'data'    => $validasi
        ], 200);
    }


    /**
     * @OA\Delete(
     *     path="/api/validasi/{id}",
     *     summary="Menghapus validasi logbook",
     *     tags={"Validasi Logbook"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroyValidasi($id)
    {
        $validasi = ValidasiLogbook::find($id);

        if (!$validasi) {
            return response()->json(['message' => 'Validasi tidak ditemukan'], 404);
        }

        $logbook = LogbookKP::find($validasi->logbook_kp_id);
        if ($logbook) {
            $logbook->status = 'Pending';
            $logbook->save();
        }

        $validasi->delete();

        return response()->json(['message' => 'Validasi berhasil dihapus'], 200);
    }
}
