<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiKP;
use App\Models\LogbookKP;
use Illuminate\Http\Request;

class EvaluasiKPController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'logbook_kp_id' => 'required|exists:logbook_k_p_s,id',
            'lecturer_id' => 'required',
            'progres_pencapaian' => 'required|string',
            'penilaian_sementara' => 'required|string',
            'catatan_pembimbing' => 'nullable|string',
            'rekomendasi_lanjutan' => 'nullable|string',
        ]);

        $data = EvaluasiKP::create($request->all());

        return response()->json([
            'message' => 'Evaluasi tengah KP berhasil dibuat',
            'data' => $data
        ], 201);
    }

    public function index()
    {
        $data = EvaluasiKP::with('logbook.student')->get();

        return response()->json([
            'message' => 'Daftar evaluasi tengah KP',
            'data' => $data
        ], 200);
    }
    public function show($id)
    {
        $data = EvaluasiKP::with('logbook.student')->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Evaluasi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail evaluasi tengah KP',
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $data = EvaluasiKP::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Evaluasi tidak ditemukan'
            ], 404);
        }

        $data->update($request->all());

        return response()->json([
            'message' => 'Evaluasi berhasil diperbarui',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $data = EvaluasiKP::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Evaluasi tidak ditemukan'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Evaluasi berhasil dihapus'
        ], 200);
    }
}
