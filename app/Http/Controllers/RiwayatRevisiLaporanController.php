<?php

namespace App\Http\Controllers;

use App\Models\LaporanKP;
use App\Models\RiwayatRevisiLaporan;
use App\Http\Requests\UploadRevisiLaporanRequest;
use App\Http\Resources\RiwayatRevisiLaporanResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RiwayatRevisiLaporanController extends Controller
{
    /**
     * Validasi format laporan
     */
    private function validateLaporanFormat($file)
    {
        if ($file->getClientOriginalExtension() !== 'pdf') {
            return false;
        }

        if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
            return false;
        }

        return true;
    }

    /**
     * List semua versi laporan untuk 1 laporan KP
     */
    public function index($laporanKpId)
    {
        $laporan = LaporanKP::findOrFail($laporanKpId);
        
        // Cek authorization - mahasiswa hanya bisa lihat laporan sendiri
        $user = Auth::user();
        if ($user->role === 'mahasiswa' && $laporan->mahasiswa_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $revisi = RiwayatRevisiLaporan::where('laporan_kp_id', $laporanKpId)
            ->orderBy('versi', 'desc')
            ->get();
        
        return response()->json([
            'laporan' => [
                'id' => $laporan->id,
                'mahasiswa_id' => $laporan->mahasiswa_id,
                'mahasiswa_name' => $laporan->mahasiswa->name ?? null,
                'status' => $laporan->status ?? 'draft',
            ],
            'total_versi' => $revisi->count(),
            'versi_final' => $revisi->where('is_final', true)->first()?->versi,
            'riwayat' => RiwayatRevisiLaporanResource::collection($revisi)
        ]);
    }

    /**
     * Upload versi baru laporan
     */
    public function store(UploadRevisiLaporanRequest $request, $laporanKpId)
    {
        $laporan = LaporanKP::findOrFail($laporanKpId);
        
        // Cek authorization - hanya mahasiswa yang bersangkutan yang bisa upload
        if (Auth::id() !== $laporan->mahasiswa_id) {
            return response()->json([
                'message' => 'Unauthorized - Hanya mahasiswa yang bersangkutan yang bisa upload'
            ], 403);
        }
        
        // Hitung versi berikutnya
        $latestVersi = RiwayatRevisiLaporan::where('laporan_kp_id', $laporanKpId)
            ->max('versi') ?? 0;
        $newVersi = $latestVersi + 1;
        
        // Upload file
        $file = $request->file('file_laporan');
        $fileName = "laporan_v{$newVersi}_" . time() . ".pdf";
        $path = $file->storeAs("laporan_kp/{$laporanKpId}", $fileName, 'public');
        
        // Validasi format
        $formatValid = $this->validateLaporanFormat($file);
        
        // Simpan ke database
        $revisi = RiwayatRevisiLaporan::create([
            'laporan_kp_id' => $laporanKpId,
            'versi' => $newVersi,
            'file_path' => $path,
            'keterangan' => $request->keterangan,
            'is_format_valid' => $formatValid,
            'is_final' => false,
        ]);
        
        return response()->json([
            'message' => "Versi {$newVersi} berhasil diupload",
            'data' => new RiwayatRevisiLaporanResource($revisi)
        ], 201);
    }

    /**
     * Detail 1 versi laporan
     */
    public function show(RiwayatRevisiLaporan $riwayatRevisi)
    {
        // Cek authorization
        $user = Auth::user();
        if ($user->role === 'mahasiswa' && $riwayatRevisi->laporanKp->mahasiswa_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return new RiwayatRevisiLaporanResource($riwayatRevisi);
    }

    /**
     * Tandai versi tertentu sebagai final
     */
    public function tandaiFinal($riwayatRevisiId)
    {
        $revisi = RiwayatRevisiLaporan::findOrFail($riwayatRevisiId);
        
        // Cek authorization - mahasiswa atau dosen pembimbing
        $user = Auth::user();
        $isMahasiswa = $user->id === $revisi->laporanKp->mahasiswa_id;
        $isPembimbing = $user->id === $revisi->laporanKp->pembimbing_id;
        
        if (!$isMahasiswa && !$isPembimbing) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Reset semua versi jadi bukan final
        RiwayatRevisiLaporan::where('laporan_kp_id', $revisi->laporan_kp_id)
            ->update(['is_final' => false]);
        
        // Tandai versi ini sebagai final
        $revisi->update(['is_final' => true]);
        
        return response()->json([
            'message' => "Versi {$revisi->versi} ditandai sebagai final"
        ]);
    }

    /**
     * Download file laporan versi tertentu
     */
    public function download(RiwayatRevisiLaporan $riwayatRevisi)
    {
        // Cek authorization
        $user = Auth::user();
        if ($user->role === 'mahasiswa' && $riwayatRevisi->laporanKp->mahasiswa_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        if (!Storage::disk('public')->exists($riwayatRevisi->file_path)) {
            return response()->json([
                'message' => 'File tidak ditemukan'
            ], 404);
        }
        
        return Storage::disk('public')->download(
            $riwayatRevisi->file_path,
            "laporan_v{$riwayatRevisi->versi}.pdf"
        );
    }

    /**
     * Hapus 1 versi laporan
     */
    public function destroy(RiwayatRevisiLaporan $riwayatRevisi)
    {
        // Cek authorization - hanya mahasiswa yang bersangkutan
        if (Auth::id() !== $riwayatRevisi->laporanKp->mahasiswa_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Cek apakah ini versi final
        if ($riwayatRevisi->is_final) {
            return response()->json([
                'message' => 'Tidak bisa menghapus versi final'
            ], 422);
        }
        
        // Cek apakah ini satu-satunya versi
        $totalVersi = RiwayatRevisiLaporan::where('laporan_kp_id', $riwayatRevisi->laporan_kp_id)->count();
        if ($totalVersi <= 1) {
            return response()->json([
                'message' => 'Tidak bisa menghapus versi terakhir'
            ], 422);
        }
        
        // Hapus file
        if (Storage::disk('public')->exists($riwayatRevisi->file_path)) {
            Storage::disk('public')->delete($riwayatRevisi->file_path);
        }
        
        // Hapus record
        $riwayatRevisi->delete();
        
        return response()->json([
            'message' => "Versi {$riwayatRevisi->versi} berhasil dihapus"
        ]);
    }

    /**
     * Compare 2 versi (metadata saja, bukan isi file)
     */
    public function compare(Request $request, $laporanKpId)
    {
        $request->validate([
            'versi_1' => 'required|integer',
            'versi_2' => 'required|integer',
        ]);
        
        $laporan = LaporanKP::findOrFail($laporanKpId);
        
        // Cek authorization
        $user = Auth::user();
        if ($user->role === 'mahasiswa' && $laporan->mahasiswa_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $revisi1 = RiwayatRevisiLaporan::where('laporan_kp_id', $laporanKpId)
            ->where('versi', $request->versi_1)
            ->firstOrFail();
            
        $revisi2 = RiwayatRevisiLaporan::where('laporan_kp_id', $laporanKpId)
            ->where('versi', $request->versi_2)
            ->firstOrFail();
        
        return response()->json([
            'versi_1' => new RiwayatRevisiLaporanResource($revisi1),
            'versi_2' => new RiwayatRevisiLaporanResource($revisi2),
            'comparison' => [
                'selisih_waktu' => $revisi2->created_at->diffForHumans($revisi1->created_at),
                'selisih_hari' => $revisi1->created_at->diffInDays($revisi2->created_at),
                'perubahan_keterangan' => [
                    'dari' => $revisi1->keterangan,
                    'ke' => $revisi2->keterangan,
                ],
                'ukuran_file' => [
                    'versi_1' => Storage::disk('public')->exists($revisi1->file_path) 
                        ? round(Storage::disk('public')->size($revisi1->file_path) / 1024 / 1024, 2) . ' MB'
                        : 'File tidak ditemukan',
                    'versi_2' => Storage::disk('public')->exists($revisi2->file_path)
                        ? round(Storage::disk('public')->size($revisi2->file_path) / 1024 / 1024, 2) . ' MB'
                        : 'File tidak ditemukan',
                ],
            ]
        ]);
    }
}