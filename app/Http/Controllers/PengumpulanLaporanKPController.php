<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanLaporanKP;
use Illuminate\Http\Request;

class PengumpulanLaporanKPController extends Controller
{
    // ===============================
    // VALIDASI FORMAT LAPORAN (PDF)
    // ===============================
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

    // ===============================
    // CEK PLAGIASI (DUMMY)
    // ===============================
    private function cekPlagiasi($file)
    {
        return rand(5, 35);
    }

    // ===============================
    // UPLOAD LAPORAN
    // ===============================
    public function upload(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required',
            'file_laporan' => 'required|file|mimes:pdf|max:10240'
        ]);

        $formatValid = $this->validateLaporanFormat($request->file('file_laporan'));
        $plagiasi = $this->cekPlagiasi($request->file('file_laporan'));

        $path = $request->file('file_laporan')->store('laporan_kp', 'public');

        $data = PengumpulanLaporanKP::create([
            'mahasiswa_id'    => $request->mahasiswa_id,
            'file_laporan'    => $path,
            'is_format_valid' => $formatValid,
            'plagiarism_score'=> $plagiasi
        ]);

        return response()->json([
            'message' => 'Laporan berhasil diupload',
            'data'    => $data
        ]);
    }

    // ===============================
    // READ ALL (FILTER + PESAN DATA KOSONG)
    // ===============================
    public function index(Request $request)
    {
        $query = PengumpulanLaporanKP::query();

        // Filter plagiasi minimal & maksimal
        if ($request->has('min_plagiasi')) {
            $query->where('plagiarism_score', '>=', $request->min_plagiasi);
        }

        if ($request->has('max_plagiasi')) {
            $query->where('plagiarism_score', '<=', $request->max_plagiasi);
        }

        // Filter format valid / tidak valid
        if ($request->has('format_valid')) {
            $isValid = $request->format_valid == 1 ? true : false;
            $query->where('is_format_valid', $isValid);
        }

        $data = $query->get([
            'id',
            'mahasiswa_id',
            'plagiarism_score',
            'is_format_valid',
            'created_at',
            'updated_at'
        ]);

        // Jika filter menghasilkan data kosong → tampilkan pesan
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data ditemukan berdasarkan filter yang diberikan',
                'data'    => []
            ], 404);
        }

        return response()->json($data);
    }

    // ===============================
    // READ BY ID
    // ===============================
    public function show($id)
    {
        $data = PengumpulanLaporanKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($data);
    }

    // ===============================
    // DELETE
    // ===============================
    public function destroy($id)
    {
        $data = PengumpulanLaporanKP::find($id);
        if (!$data) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $data->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }

    // ===============================
    // UPDATE LAPORAN
    // ===============================
   public function update(Request $request, $id)
{
    $data = PengumpulanLaporanKP::find($id);

    if (!$data) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $request->validate([
        'mahasiswa_id' => 'required',
        'file_laporan' => 'nullable|file|mimes:pdf|max:10240'
    ]);

    // Update field dasar
    $data->mahasiswa_id = $request->mahasiswa_id;

    // Jika ada file baru, proses upload baru
    if ($request->hasFile('file_laporan')) {

        $file = $request->file('file_laporan');

        // Validasi format
        $formatValid = $this->validateLaporanFormat($file);

        // Plagiasi baru
        $plagiasi = $this->cekPlagiasi($file);

        // Hapus file lama jika ada
        if ($data->file_laporan && \Storage::disk('public')->exists($data->file_laporan)) {
            \Storage::disk('public')->delete($data->file_laporan);
        }

        // Upload file baru
        $path = $file->store('laporan_kp', 'public');

        // Update data file
        $data->file_laporan = $path;
        $data->is_format_valid = $formatValid;
        $data->plagiarism_score = $plagiasi;
    }

    // Simpan perubahan ke database
    $data->save();

    return response()->json([
        'message' => 'Laporan berhasil diperbarui',
        'data'    => $data
    ]);
}
}
