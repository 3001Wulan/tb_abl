<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerifikasiAdministrasi;
use App\Models\PendaftaranKP;

class VerifikasiAdministrasiController extends Controller
{
    /**
     * 1. CREATE - Buat Verifikasi Baru
     * POST /api/verifikasi-admin
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'pendaftaran_kp_id' => 'required|exists:pendaftaran_kp,id'
            ]);

            $pendaftaran = PendaftaranKP::findOrFail($request->pendaftaran_kp_id);
            
            $nomor = 'VA-' . date('YmdHis') . '-' . rand(1000, 9999);

            $verifikasi = VerifikasiAdministrasi::create([
                'pendaftaran_kp_id' => $request->pendaftaran_kp_id,
                'nomor_verifikasi' => $nomor,
                'nama_mahasiswa' => $pendaftaran->nama_mahasiswa,
                'nim' => $pendaftaran->nim,
                'jurusan' => $pendaftaran->jurusan,
                'status_verifikasi' => 'pending',
                'status_persetujuan' => 'pending',
                'status_mahasiswa' => 'menunggu verifikasi'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi administrasi berhasil dibuat',
                'data' => $verifikasi
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. GET ALL - Lihat Semua Verifikasi
     * GET /api/verifikasi-admin
     */
    public function index()
    {
        try {
            $verifikasi = VerifikasiAdministrasi::all();

            return response()->json([
                'success' => true,
                'total' => $verifikasi->count(),
                'data' => $verifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. GET DETAIL - Lihat Detail Verifikasi
     * GET /api/verifikasi-admin/{id}
     */
    public function show($id)
    {
        try {
            $verifikasi = VerifikasiAdministrasi::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $verifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * 4. CEK KELENGKAPAN - Periksa Kelengkapan Berkas
     * POST /api/verifikasi-admin/{id}/cek-kelengkapan
     */
    public function cekKelengkapan(Request $request, $id)
    {
        try {
            $request->validate([
                'krs_lengkap' => 'required|boolean',
                'transkrip_lengkap' => 'required|boolean',
                'proposal_lengkap' => 'required|boolean',
                'catatan_kelengkapan' => 'nullable|string'
            ]);

            $verifikasi = VerifikasiAdministrasi::findOrFail($id);

            $verifikasi->update([
                'krs_lengkap' => $request->krs_lengkap,
                'transkrip_lengkap' => $request->transkrip_lengkap,
                'proposal_lengkap' => $request->proposal_lengkap,
                'catatan_kelengkapan' => $request->catatan_kelengkapan,
                'tanggal_verifikasi' => now()
            ]);

            // Update status verifikasi
            $semuaLengkap = $request->krs_lengkap && $request->transkrip_lengkap && $request->proposal_lengkap;
            $verifikasi->status_verifikasi = $semuaLengkap ? 'lengkap' : 'tidak_lengkap';
            $verifikasi->save();

            return response()->json([
                'success' => true,
                'message' => 'Cek kelengkapan berhasil',
                'data' => $verifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 5. SETUJUI - Menyetujui Pengajuan
     * POST /api/verifikasi-admin/{id}/setujui
     */
    public function setujui(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_pemeriksa' => 'required|string'
            ]);

            $verifikasi = VerifikasiAdministrasi::findOrFail($id);

            if ($verifikasi->status_verifikasi !== 'lengkap') {
                return response()->json([
                    'success' => false,
                    'message' => 'Berkas belum lengkap, tidak bisa disetujui'
                ], 422);
            }

            $verifikasi->update([
                'status_persetujuan' => 'disetujui',
                'nama_pemeriksa' => $request->nama_pemeriksa,
                'tanggal_persetujuan' => now(),
                'status_mahasiswa' => 'pengajuan diterima',
                'pesan_ke_mahasiswa' => 'Pengajuan KP Anda telah diterima. Silahkan lanjut ke tahap penempatan.'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disetujui',
                'data' => $verifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. TOLAK - Menolak Pengajuan
     * POST /api/verifikasi-admin/{id}/tolak
     */
    public function tolak(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan_tolak' => 'required|string',
                'nama_pemeriksa' => 'required|string'
            ]);

            $verifikasi = VerifikasiAdministrasi::findOrFail($id);

            $verifikasi->update([
                'status_persetujuan' => 'ditolak',
                'alasan_tolak' => $request->alasan_tolak,
                'nama_pemeriksa' => $request->nama_pemeriksa,
                'tanggal_persetujuan' => now(),
                'status_mahasiswa' => 'pengajuan ditolak',
                'pesan_ke_mahasiswa' => 'Pengajuan KP Anda ditolak. Alasan: ' . $request->alasan_tolak
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil ditolak',
                'data' => $verifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 7. GET STATUS MAHASISWA - Lihat Status Pengajuan
     * GET /api/verifikasi-admin/{id}/status-mahasiswa
     */
    public function getStatusMahasiswa($id)
    {
        try {
            $verifikasi = VerifikasiAdministrasi::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'nim' => $verifikasi->nim,
                    'nama_mahasiswa' => $verifikasi->nama_mahasiswa,
                    'status_mahasiswa' => $verifikasi->status_mahasiswa,
                    'pesan_ke_mahasiswa' => $verifikasi->pesan_ke_mahasiswa
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
}