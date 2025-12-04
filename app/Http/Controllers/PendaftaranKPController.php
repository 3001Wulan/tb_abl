<?php
// File: app/Http/Controllers/PendaftaranKPController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendaftaranKP;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PendaftaranKPController extends Controller
{
    /**
     * 1. FORMULIR PENDAFTARAN (CREATE)
     * POST /api/kp/daftar
     */
    public function daftar(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama_mahasiswa' => 'required|string',
                'nim' => 'required|string|unique:pendaftaran_kp,nim',
                'email' => 'required|email|unique:pendaftaran_kp,email',
                'no_hp' => 'required|string',
                'jurusan' => 'required|string',
                'universitas' => 'required|string',
                'tema_kp' => 'nullable|string',
                'perusahaan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create pendaftaran
            $pendaftaran = PendaftaranKP::create([
                'nama_mahasiswa' => $request->nama_mahasiswa,
                'nim' => $request->nim,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'jurusan' => $request->jurusan,
                'universitas' => $request->universitas,
                'tema_kp' => $request->tema_kp ?? 'Belum ditentukan',
                'perusahaan' => $request->perusahaan ?? 'Belum ditentukan',
                'status_validasi' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran formulir berhasil dibuat',
                'data' => $pendaftaran
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. UPLOAD BERKAS
     * POST /api/kp/upload/{id}
     */
    public function uploadBerkas(Request $request, $id)
    {
        try {
            $pendaftaran = PendaftaranKP::findOrFail($id);

            // Validasi file
            $validator = Validator::make($request->all(), [
                'krs' => 'nullable|mimes:pdf,doc,docx|max:5120',
                'transkrip' => 'nullable|mimes:pdf,doc,docx|max:5120',
                'proposal' => 'nullable|mimes:pdf,doc,docx|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi file gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Upload KRS
            if ($request->hasFile('krs')) {
                $krsFile = $request->file('krs');
                $krsPath = $krsFile->store('pendaftaran_kp/krs', 'public');
                $pendaftaran->krs = $krsPath;
            }

            // Upload Transkrip
            if ($request->hasFile('transkrip')) {
                $transkripFile = $request->file('transkrip');
                $transkripPath = $transkripFile->store('pendaftaran_kp/transkrip', 'public');
                $pendaftaran->transkrip = $transkripPath;
            }

            // Upload Proposal
            if ($request->hasFile('proposal')) {
                $proposalFile = $request->file('proposal');
                $proposalPath = $proposalFile->store('pendaftaran_kp/proposal', 'public');
                $pendaftaran->proposal = $proposalPath;
            }

            $pendaftaran->save();

            return response()->json([
                'success' => true,
                'message' => 'Berkas berhasil diupload',
                'data' => $pendaftaran
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

    /**
     * 3. VALIDASI BERKAS
     * POST /api/kp/validasi/{id}
     */
    public function validasi(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status_validasi' => 'required|in:pending,valid,invalid',
                'catatan_validasi' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pendaftaran = PendaftaranKP::findOrFail($id);

            // Cek apakah semua berkas sudah diupload
            if ($request->status_validasi == 'valid' && (!$pendaftaran->krs || !$pendaftaran->transkrip || !$pendaftaran->proposal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak semua berkas telah diupload'
                ], 422);
            }

            $pendaftaran->status_validasi = $request->status_validasi;
            $pendaftaran->catatan_validasi = $request->catatan_validasi;
            $pendaftaran->save();

            return response()->json([
                'success' => true,
                'message' => 'Status validasi berhasil diperbarui',
                'data' => $pendaftaran
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

    /**
     * 4. GET DETAIL PENDAFTARAN
     * GET /api/kp/detail/{id}
     */
    public function detail($id)
    {
        try {
            $pendaftaran = PendaftaranKP::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pendaftaran
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan'
            ], 404);
        }
    }

    /**
     * 5. GET SEMUA PENDAFTARAN
     * GET /api/kp/daftar-semua
     */
    public function daftarSemua()
    {
        try {
            $pendaftaran = PendaftaranKP::all();

            return response()->json([
                'success' => true,
                'total' => $pendaftaran->count(),
                'data' => $pendaftaran
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. UPDATE PENDAFTARAN
     * PUT /api/kp/update/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_mahasiswa' => 'sometimes|string',
                'email' => 'sometimes|email',
                'no_hp' => 'sometimes|string',
                'tema_kp' => 'sometimes|string',
                'perusahaan' => 'sometimes|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pendaftaran = PendaftaranKP::findOrFail($id);
            $pendaftaran->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $pendaftaran
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

    /**
     * 7. DELETE PENDAFTARAN
     * DELETE /api/kp/hapus/{id}
     */
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