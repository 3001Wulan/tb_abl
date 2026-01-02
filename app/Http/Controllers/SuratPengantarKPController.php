<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratPengantarKP;
use App\Models\PendaftaranKP;
use Illuminate\Support\Facades\Storage;

class SuratPengantarKPController extends Controller
{
   /**
 * @OA\Post(
 *     path="/api/surat-pengantar",
 *     tags={"Surat Pengantar KP"},
 *     summary="Ajukan surat pengantar KP",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"pendaftaran_kp_id","nama_perusahaan"},
 *             @OA\Property(property="pendaftaran_kp_id", type="integer", example=1),
 *             @OA\Property(property="nama_perusahaan", type="string", example="PT. ABC"),
 *             @OA\Property(property="alamat_perusahaan", type="string", example="Jl. Sudirman No.1"),
 *             @OA\Property(property="kontak_perusahaan", type="string", example="08123456789"),
 *             @OA\Property(property="catatan_pengajuan", type="string", example="Mohon segera diproses"),
 *             @OA\Property(property="jurusan", type="string", example="Teknik Informatika"),
 *             @OA\Property(property="nama_pembimbing_akademik", type="string", example="Dr. Budi"),
 *             @OA\Property(property="nip_pembimbing", type="string", example="12345678")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Berhasil membuat pengajuan surat"),
 *     @OA\Response(response=422, description="Validasi gagal"),
 *     @OA\Response(response=500, description="Terjadi kesalahan server")
 * )
 */

   public function store(Request $request)
{
    $request->validate([
        'pendaftaran_kp_id' => 'required|exists:pendaftaran_kp,id',
        'jurusan' => 'nullable|string',
        'nama_pembimbing_akademik' => 'nullable|string',
        'nip_pembimbing' => 'nullable|string',
    ]);

    $surat = SuratPengantarKP::create([
        'pendaftaran_kp_id' => $request->pendaftaran_kp_id,
        'nomor_surat' => 'SURAT-'.time(), // otomatis generate nomor surat
        'jurusan' => $request->jurusan ?? null,
        'nama_pembimbing_akademik' => $request->nama_pembimbing_akademik ?? null,
        'nip_pembimbing' => $request->nip_pembimbing ?? null,
        'status_pengajuan' => 'pending',
        'status_penandatanganan' => 'menunggu',
        'file_path' => null,
        'nama_file_pdf' => null,
        'nama_penandatangan' => null,
        'jabatan_penandatangan' => null,
        'tanggal_penandatanganan' => null,
    ]);

    return response()->json([
        'success' => true,
        'data' => $surat
    ], 201);
}

    /**
     * @OA\Get(
     *     path="/api/surat-pengantar",
     *     tags={"Surat Pengantar KP"},
     *     summary="Lihat semua pengajuan surat",
     *     @OA\Response(response=200, description="Berhasil mendapatkan data"),
     *     @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function index()
    {
        $surat = SuratPengantarKP::all();
        return response()->json(['success' => true, 'data' => $surat], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/surat-pengantar/{id}",
     *     tags={"Surat Pengantar KP"},
     *     summary="Lihat detail pengajuan surat",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Berhasil mendapatkan data"),
     *     @OA\Response(response=404, description="Data tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $surat = SuratPengantarKP::find($id);
        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $surat], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/surat-pengantar/{id}/buat-pdf",
     *     tags={"Surat Pengantar KP"},
     *     summary="Buat file PDF dari pengajuan surat",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="PDF berhasil dibuat"),
     *     @OA\Response(response=422, description="Surat tidak dalam status pending"),
     *     @OA\Response(response=404, description="Data tidak ditemukan"),
     *     @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function buatPDF($id)
    {
        $surat = SuratPengantarKP::find($id);
        if (!$surat) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        if ($surat->status != 'pending') return response()->json(['success' => false, 'message' => 'Surat tidak dalam status pending'], 422);

        // Generate PDF dummy
        $path = 'surat_kp_'.$id.'.pdf';
        Storage::put($path, 'Isi PDF surat pengantar KP #'.$id);

        $surat->status = 'proses';
        $surat->save();

        return response()->json(['success' => true, 'message' => 'PDF berhasil dibuat', 'path' => $path], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/surat-pengantar/{id}/tandatangani",
     *     tags={"Surat Pengantar KP"},
     *     summary="Tandatangani surat pengantar oleh Kaprodi/Jurusan",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_penandatangan","jabatan_penandatangan"},
     *             @OA\Property(property="nama_penandatangan", type="string", example="Dr. Budi"),
     *             @OA\Property(property="jabatan_penandatangan", type="string", example="Kaprodi TI")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Surat berhasil ditandatangani"),
     *     @OA\Response(response=422, description="Surat tidak dalam status proses"),
     *     @OA\Response(response=404, description="Data tidak ditemukan"),
     *     @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function tandatangani(Request $request, $id)
    {
        $request->validate([
            'nama_penandatangan' => 'required|string',
            'jabatan_penandatangan' => 'required|string',
        ]);

        $surat = SuratPengantarKP::find($id);
        if (!$surat) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        if ($surat->status != 'proses') return response()->json(['success' => false, 'message' => 'Surat tidak dalam status proses'], 422);

        $surat->nama_penandatangan = $request->nama_penandatangan;
        $surat->jabatan_penandatangan = $request->jabatan_penandatangan;
        $surat->status = 'selesai';
        $surat->save();

        return response()->json(['success' => true, 'message' => 'Surat berhasil ditandatangani'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/surat-pengantar/{id}/tolak",
     *     tags={"Surat Pengantar KP"},
     *     summary="Tolak pengajuan surat pengantar",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"alasan_penolakan"},
     *             @OA\Property(property="alasan_penolakan", type="string", example="Dokumen tidak lengkap")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pengajuan surat berhasil ditolak"),
     *     @OA\Response(response=422, description="Surat tidak dalam status pending"),
     *     @OA\Response(response=404, description="Data tidak ditemukan"),
     *     @OA\Response(response=500, description="Terjadi kesalahan server")
     * )
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $surat = SuratPengantarKP::find($id);
        if (!$surat) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        if ($surat->status != 'pending') return response()->json(['success' => false, 'message' => 'Surat tidak dalam status pending'], 422);

        $surat->alasan_penolakan = $request->alasan_penolakan;
        $surat->status = 'ditolak';
        $surat->save();

        return response()->json(['success' => true, 'message' => 'Pengajuan surat berhasil ditolak'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/surat-pengantar/{id}/download",
     *     tags={"Surat Pengantar KP"},
     *     summary="Download surat pengantar",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Surat siap diunduh"),
     *     @OA\Response(response=422, description="Surat belum selesai/ditandatangani"),
     *     @OA\Response(response=404, description="Data/file tidak ditemukan")
     * )
     */
    public function download($id)
    {
        $surat = SuratPengantarKP::find($id);
        if (!$surat) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        if ($surat->status != 'selesai') return response()->json(['success' => false, 'message' => 'Surat belum selesai/ditandatangani'], 422);

        $path = 'surat_kp_'.$id.'.pdf';
        if (!Storage::exists($path)) return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);

        return response()->download(storage_path('app/'.$path));
    }

    /**
     * @OA\Get(
     *     path="/api/surat-pengantar/{id}/status",
     *     tags={"Surat Pengantar KP"},
     *     summary="Lihat status pengajuan surat",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Berhasil mendapatkan status"),
     *     @OA\Response(response=404, description="Data tidak ditemukan")
     * )
     */
    public function getStatus($id)
{
    $surat = SuratPengantarKP::find($id);
    if (!$surat) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);

    return response()->json([
        'success' => true,
        'status' => $surat->status_pengajuan, // sesuaikan dengan nama kolom
        'pesan' => $this->getPesanStatus($surat->status_pengajuan)
    ], 200);
}

// Helper untuk generate pesan status
private function getPesanStatus($status)
{
    $pesan = [
        'pending' => 'Menunggu persetujuan dari jurusan',
        'proses' => 'Sedang diproses, menunggu penandatanganan',
        'selesai' => 'Surat sudah selesai dan siap diunduh',
        'ditolak' => 'Pengajuan surat ditolak'
    ];
    return $pesan[$status] ?? 'Status tidak diketahui';
}

}
