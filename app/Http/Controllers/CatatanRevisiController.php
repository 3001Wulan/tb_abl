<?php

namespace App\Http\Controllers;

use App\Models\AdministrasiSeminar;
use App\Models\CatatanRevisi;
use App\Http\Requests\CatatanRevisiRequest;
use App\Http\Requests\UpdateProgressRequest;
use App\Http\Resources\CatatanRevisiResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatatanRevisiController extends Controller
{
    /**
     * List catatan revisi untuk 1 seminar
     */
    public function index(AdministrasiSeminar $administrasiSeminar)
    {
        $catatan = CatatanRevisi::where('administrasi_seminar_id', $administrasiSeminar->id)
            ->orderBy('tipe', 'asc') // wajib dulu, baru saran
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'administrasi_seminar_id' => $administrasiSeminar->id,
            'mahasiswa' => [
                'id' => $administrasiSeminar->mahasiswa->id,
                'name' => $administrasiSeminar->mahasiswa->name,
            ],
            'komentar_umum' => $administrasiSeminar->komentar_umum,
            'revisi' => [
                'wajib' => CatatanRevisiResource::collection($catatan->where('tipe', 'wajib')),
                'saran' => CatatanRevisiResource::collection($catatan->where('tipe', 'saran')),
            ],
            'progress' => [
                'wajib_selesai' => $catatan->where('tipe', 'wajib')->where('is_done', true)->count(),
                'wajib_total' => $catatan->where('tipe', 'wajib')->count(),
                'saran_selesai' => $catatan->where('tipe', 'saran')->where('is_done', true)->count(),
                'saran_total' => $catatan->where('tipe', 'saran')->count(),
            ],
            'revisi_approved' => !is_null($administrasiSeminar->revisi_approved_at),
            'revisi_approved_at' => $administrasiSeminar->revisi_approved_at,
        ]);
    }

    /**
     * Simpan catatan revisi dari dosen pembimbing
     */
    public function store(CatatanRevisiRequest $request, AdministrasiSeminar $administrasiSeminar)
    {
        // Cek authorization - hanya pembimbing yang bisa input
        if (Auth::id() !== $administrasiSeminar->pembimbing_id) {
            return response()->json([
                'message' => 'Unauthorized - Hanya dosen pembimbing yang bisa input catatan revisi'
            ], 403);
        }

        DB::transaction(function () use ($request, $administrasiSeminar) {
            // Hapus catatan lama
            CatatanRevisi::where('administrasi_seminar_id', $administrasiSeminar->id)->delete();
            
            // Simpan perbaikan wajib
            if ($request->has('perbaikan_wajib')) {
                foreach ($request->perbaikan_wajib as $item) {
                    if (!empty($item)) {
                        CatatanRevisi::create([
                            'administrasi_seminar_id' => $administrasiSeminar->id,
                            'tipe' => 'wajib',
                            'deskripsi' => $item,
                        ]);
                    }
                }
            }
            
            // Simpan saran perbaikan
            if ($request->has('saran_perbaikan')) {
                foreach ($request->saran_perbaikan as $item) {
                    if (!empty($item)) {
                        CatatanRevisi::create([
                            'administrasi_seminar_id' => $administrasiSeminar->id,
                            'tipe' => 'saran',
                            'deskripsi' => $item,
                        ]);
                    }
                }
            }
            
            // Update komentar umum
            $administrasiSeminar->update([
                'komentar_umum' => $request->komentar_umum
            ]);
        });
        
        return response()->json([
            'message' => 'Catatan revisi berhasil disimpan'
        ]);
    }

    /**
     * Update progress revisi (mahasiswa centang item yang sudah dikerjakan)
     */
    public function updateProgress(UpdateProgressRequest $request, CatatanRevisi $catatanRevisi)
    {
        // Cek authorization - hanya mahasiswa yang bersangkutan
        if (Auth::id() !== $catatanRevisi->administrasiSeminar->mahasiswa_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $catatanRevisi->update([
            'is_done' => $request->is_done
        ]);
        
        return response()->json([
            'message' => 'Progress diperbarui',
            'data' => new CatatanRevisiResource($catatanRevisi)
        ]);
    }

    /**
     * Approve revisi oleh dosen pembimbing
     */
    public function approve(AdministrasiSeminar $administrasiSeminar)
    {
        // Cek authorization
        if (Auth::id() !== $administrasiSeminar->pembimbing_id) {
            return response()->json([
                'message' => 'Unauthorized - Hanya dosen pembimbing yang bisa approve'
            ], 403);
        }

        // Cek apakah semua perbaikan wajib sudah selesai
        $wajibBelumSelesai = CatatanRevisi::where('administrasi_seminar_id', $administrasiSeminar->id)
            ->where('tipe', 'wajib')
            ->where('is_done', false)
            ->count();
            
        if ($wajibBelumSelesai > 0) {
            return response()->json([
                'message' => 'Masih ada perbaikan wajib yang belum selesai',
                'jumlah_belum_selesai' => $wajibBelumSelesai
            ], 422);
        }
        
        $administrasiSeminar->update([
            'revisi_approved_at' => now()
        ]);
        
        return response()->json([
            'message' => 'Revisi telah disetujui'
        ]);
    }

    /**
     * Batalkan approval revisi
     */
    public function batalkanApproval(AdministrasiSeminar $administrasiSeminar)
    {
        // Cek authorization
        if (Auth::id() !== $administrasiSeminar->pembimbing_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $administrasiSeminar->update([
            'revisi_approved_at' => null
        ]);
        
        return response()->json([
            'message' => 'Approval revisi dibatalkan'
        ]);
    }

    /**
     * Hapus 1 item catatan revisi
     */
    public function destroy(CatatanRevisi $catatanRevisi)
    {
        // Cek authorization - hanya pembimbing yang bisa hapus
        if (Auth::id() !== $catatanRevisi->administrasiSeminar->pembimbing_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $catatanRevisi->delete();
        
        return response()->json([
            'message' => 'Catatan revisi berhasil dihapus'
        ]);
    }
}