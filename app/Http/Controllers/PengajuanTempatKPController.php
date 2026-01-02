<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanTempatKP;
use App\Models\PendaftaranKP;

class PengajuanTempatKPController extends Controller
{
   
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'pendaftaran_kp_id' => 'required|exists:pendaftaran_kp,id',
                'nama_instansi' => 'required|string|max:255',
                'alamat_instansi' => 'nullable|string',
                'kontak_instansi' => 'nullable|string|max:20',
                'bidang_usaha' => 'nullable|string|max:255',
                'catatan_pengajuan' => 'nullable|string'
            ]);

            $pendaftaran = PendaftaranKP::findOrFail($validated['pendaftaran_kp_id']);

            $pengajuan = PengajuanTempatKP::create([
                'pendaftaran_kp_id' => $validated['pendaftaran_kp_id'],
                'nama_mahasiswa' => $pendaftaran->nama_mahasiswa,
                'nim' => $pendaftaran->nim,
                'nama_instansi' => $validated['nama_instansi'],
                'alamat_instansi' => $validated['alamat_instansi'] ?? null,
                'kontak_instansi' => $validated['kontak_instansi'] ?? null,
                'bidang_usaha' => $validated['bidang_usaha'] ?? null,
                'catatan_pengajuan' => $validated['catatan_pengajuan'] ?? null,
                'tanggal_pengajuan' => now()->toDateString(),
                'status_pengajuan' => 'pending',
                'status_persetujuan_instansi' => 'menunggu'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan tempat KP berhasil dibuat',
                'data' => $pengajuan
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $pengajuan = PengajuanTempatKP::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'total' => $pengajuan->count(),
                'data' => $pengajuan
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $pengajuan = PengajuanTempatKP::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pengajuan
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function persetujuanInstansi(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status_persetujuan' => 'required|in:diterima,ditolak',
                'nama_pembimbing_instansi' => 'nullable|string|max:255',
                'kontak_pembimbing' => 'nullable|string|max:20',
                'catatan_instansi' => 'nullable|string'
            ]);

            $pengajuan = PengajuanTempatKP::findOrFail($id);

            if ($pengajuan->status_pengajuan !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan harus dalam status pending untuk disetujui/ditolak instansi'
                ], 422);
            }

            if ($validated['status_persetujuan'] === 'diterima') {
                $pengajuan->update([
                    'status_persetujuan_instansi' => 'diterima',
                    'status_pengajuan' => 'diterima_instansi',
                    'nama_pembimbing_instansi' => $validated['nama_pembimbing_instansi'] ?? null,
                    'kontak_pembimbing' => $validated['kontak_pembimbing'] ?? null,
                    'catatan_instansi' => $validated['catatan_instansi'] ?? null,
                    'tanggal_persetujuan_instansi' => now()->toDateString()
                ]);
            } else {
                $pengajuan->update([
                    'status_persetujuan_instansi' => 'ditolak',
                    'status_pengajuan' => 'ditolak_instansi',
                    'catatan_instansi' => $validated['catatan_instansi'] ?? null,
                    'tanggal_persetujuan_instansi' => now()->toDateString()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status persetujuan instansi berhasil diupdate',
                'data' => $pengajuan
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function persetujuanJurusan(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status_persetujuan' => 'required|in:disetujui,ditolak',
                'catatan_jurusan' => 'nullable|string'
            ]);

            $pengajuan = PengajuanTempatKP::findOrFail($id);

            if ($pengajuan->status_pengajuan !== 'diterima_instansi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan harus diterima instansi terlebih dahulu'
                ], 422);
            }

            if ($validated['status_persetujuan'] === 'disetujui') {
                $pengajuan->update([
                    'status_pengajuan' => 'disetujui',
                    'catatan_jurusan' => $validated['catatan_jurusan'] ?? null,
                    'tanggal_persetujuan_jurusan' => now()->toDateString()
                ]);
            } else {
                $pengajuan->update([
                    'status_pengajuan' => 'ditolak_jurusan',
                    'catatan_jurusan' => $validated['catatan_jurusan'] ?? null,
                    'tanggal_persetujuan_jurusan' => now()->toDateString()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status persetujuan jurusan berhasil diupdate',
                'data' => $pengajuan
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatus($id)
    {
        try {
            $pengajuan = PengajuanTempatKP::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengajuan->id,
                    'nama_mahasiswa' => $pengajuan->nama_mahasiswa,
                    'nama_instansi' => $pengajuan->nama_instansi,
                    'status_pengajuan' => $pengajuan->status_pengajuan,
                    'status_persetujuan_instansi' => $pengajuan->status_persetujuan_instansi,
                    'pesan_status' => $this->getPesanStatus($pengajuan->status_pengajuan)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reset($id)
    {
        try {
            $pengajuan = PengajuanTempatKP::findOrFail($id);

            $pengajuan->update([
                'status_pengajuan' => 'pending',
                'status_persetujuan_instansi' => 'menunggu',
                'nama_pembimbing_instansi' => null,
                'kontak_pembimbing' => null,
                'catatan_instansi' => null,
                'catatan_jurusan' => null,
                'tanggal_persetujuan_instansi' => null,
                'tanggal_persetujuan_jurusan' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil direset ke status pending',
                'data' => $pengajuan
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getPesanStatus($status)
    {
        $pesan = [
            'pending' => 'Menunggu persetujuan dari instansi',
            'diterima_instansi' => 'Diterima instansi, menunggu persetujuan jurusan',
            'disetujui' => 'Pengajuan disetujui, tempat KP sudah pasti',
            'ditolak_instansi' => 'Ditolak oleh instansi',
            'ditolak_jurusan' => 'Ditolak oleh jurusan'
        ];

        return $pesan[$status] ?? 'Status tidak diketahui';
    }
}