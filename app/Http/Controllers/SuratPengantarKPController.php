<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratPengantarKP;
use App\Models\PendaftaranKP;

class SuratPengantarKPController extends Controller
{
    /**
     * 1. CREATE - Mahasiswa Mengajukan Surat
     * POST /api/surat-pengantar
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'pendaftaran_kp_id' => 'required|exists:pendaftaran_kp,id',
                'nama_perusahaan' => 'required|string',
                'alamat_perusahaan' => 'nullable|string',
                'kontak_perusahaan' => 'nullable|string',
                'catatan_pengajuan' => 'nullable|string'
            ]);

            $pendaftaran = PendaftaranKP::findOrFail($request->pendaftaran_kp_id);
            
            $nomor = 'SP-' . date('YmdHis') . '-' . rand(1000, 9999);

            $surat = SuratPengantarKP::create([
                'pendaftaran_kp_id' => $request->pendaftaran_kp_id,
                'nomor_surat' => $nomor,
                'nama_mahasiswa' => $pendaftaran->nama_mahasiswa,
                'nim' => $pendaftaran->nim,
                'jurusan' => $pendaftaran->jurusan,
                'universitas' => $pendaftaran->universitas,
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat_perusahaan' => $request->alamat_perusahaan,
                'kontak_perusahaan' => $request->kontak_perusahaan,
                'catatan_pengajuan' => $request->catatan_pengajuan,
                'tanggal_pengajuan' => now()->toDateString(),
                'status_pengajuan' => 'pending',
                'status_penandatanganan' => 'menunggu'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan surat pengantar berhasil dibuat',
                'data' => $surat
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. GET ALL - Lihat Semua Pengajuan Surat
     * GET /api/surat-pengantar
     */
    public function index()
    {
        try {
            $surat = SuratPengantarKP::all();

            return response()->json([
                'success' => true,
                'total' => $surat->count(),
                'data' => $surat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. GET DETAIL - Lihat Detail Pengajuan Surat
     * GET /api/surat-pengantar/{id}
     */
    public function show($id)
    {
        try {
            $surat = SuratPengantarKP::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $surat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * 4. BUAT PDF - Jurusan Membuat File PDF
     * POST /api/surat-pengantar/{id}/buat-pdf
     */
    public function buatPDF($id)
    {
        try {
            $surat = SuratPengantarKP::findOrFail($id);

            // Hanya bisa buat PDF jika status pending
            if ($surat->status_pengajuan !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat harus dalam status pending untuk membuat PDF'
                ], 422);
            }

            // Simulasi pembuatan PDF
            $namaFile = 'SP-' . $surat->nim . '-' . date('YmdHis') . '.pdf';
            $filePath = 'surat_pengantar/' . $namaFile;

            $surat->file_path = $filePath;
            $surat->nama_file_pdf = $namaFile;
            $surat->status_pengajuan = 'proses';
            $surat->save();

            return response()->json([
                'success' => true,
                'message' => 'PDF surat berhasil dibuat',
                'data' => $surat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 5. TANDATANGANI - Tanda Tangan Kaprodi/Jurusan
     * POST /api/surat-pengantar/{id}/tandatangani
     */
    public function tandatangani(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_penandatangan' => 'required|string',
                'jabatan_penandatangan' => 'required|string'
            ]);

            $surat = SuratPengantarKP::findOrFail($id);

            // Hanya bisa tandatangan jika status proses
            if ($surat->status_pengajuan !== 'proses') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat harus dalam status proses untuk ditandatangani'
                ], 422);
            }

            $surat->nama_penandatangan = $request->nama_penandatangan;
            $surat->jabatan_penandatangan = $request->jabatan_penandatangan;
            $surat->tanggal_penandatanganan = now()->toDateString();
            $surat->status_penandatanganan = 'sudah_ditandatangani';
            $surat->status_pengajuan = 'selesai';
            $surat->tanggal_selesai = now()->toDateString();
            $surat->save();

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil ditandatangani',
                'data' => $surat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. TOLAK - Tolak Pengajuan Surat
     * POST /api/surat-pengantar/{id}/tolak
     */
    public function tolak(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan_penolakan' => 'required|string'
            ]);

            $surat = SuratPengantarKP::findOrFail($id);

            // Hanya bisa tolak jika status pending
            if ($surat->status_pengajuan !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat harus dalam status pending untuk ditolak'
                ], 422);
            }

            $surat->status_pengajuan = 'ditolak';
            $surat->alasan_penolakan = $request->alasan_penolakan;
            $surat->save();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan surat berhasil ditolak',
                'data' => $surat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 7. DOWNLOAD - Mahasiswa Mengunduh Surat
     * GET /api/surat-pengantar/{id}/download
     */
    public function download($id)
    {
        try {
            $surat = SuratPengantarKP::findOrFail($id);

            // Hanya bisa download jika status selesai
            if ($surat->status_pengajuan !== 'selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat belum selesai/ditandatangani'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Surat siap diunduh',
                'data' => [
                    'nomor_surat' => $surat->nomor_surat,
                    'nama_file' => $surat->nama_file_pdf,
                    'file_path' => $surat->file_path,
                    'download_url' => '/storage/' . $surat->file_path
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * 8. GET STATUS - Lihat Status Surat
     * GET /api/surat-pengantar/{id}/status
     */
    public function getStatus($id)
    {
        try {
            $surat = SuratPengantarKP::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'nomor_surat' => $surat->nomor_surat,
                    'nama_mahasiswa' => $surat->nama_mahasiswa,
                    'status_pengajuan' => $surat->status_pengajuan,
                    'status_penandatanganan' => $surat->status_penandatanganan,
                    'pesan_status' => $this->getPesanStatus($surat->status_pengajuan)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Helper: Generate Pesan Status
     */
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