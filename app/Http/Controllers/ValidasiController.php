<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LogbookKP; 
use App\Models\ValidasiLogbook;
use App\Models\EvaluasiTengahKP;

class ValidasiController extends Controller {

    // ==== Layanan 4: Validasi Kegiatan KP (Mingguan/Harian - Dosen) ====

    /**
     * Dosen memvalidasi Logbook tertentu (Pemeriksaan kesesuaian, memberikan feedback).
     * @param Request $request
     * @param int $logbookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeValidasi(Request $request, $logbookId) {
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'status_validasi' => 'required|in:Disetujui,Ditolak', 
            'catatan_pembimbing' => 'nullable', // Feedback dan catatan perbaikan
        ]);

        // Menggunakan updateOrCreate agar dosen bisa merevisi validasi (penting untuk feedback)
        $validasi = ValidasiLogbook::updateOrCreate(
            ['logbook_kp_id' => (int)$logbookId], 
            [ 
                'lecturer_id' => $request->lecturer_id,
                'status_validasi' => $request->status_validasi,
                'catatan_pembimbing' => $request->catatan_pembimbing,
            ]
        );
        
        // Update status di tabel LogbookKP (penting untuk Tracking Progres di Layanan 3)
        $logbook = LogbookKP::find($logbookId);
        if ($logbook) {
            $logbook->status = $request->status_validasi;
            $logbook->save();
        }

        return response()->json($validasi, 201);
    }

    /**
     * Mengambil status validasi untuk Logbook tertentu.
     * @param int $logbookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexValidasi($logbookId) {
        return response()->json(ValidasiLogbook::where('logbook_kp_id', $logbookId)->get());
    }

    // ==== Layanan 5: Evaluasi Tengah KP (Periode Tertentu - Dosen) ====

    /**
     * Dosen menyimpan hasil Evaluasi Tengah KP (Penilaian Sementara & Review Progres).
     * @param Request $request
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEvaluasiTengah(Request $request, $studentId) {
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'skor_progres' => 'required|integer|min:0|max:100', // Penilaian sementara
            'catatan_umum' => 'required', // Review progres dan catatan evaluasi
        ]);

        // Menggunakan updateOrCreate untuk memastikan hanya ada satu evaluasi tengah per mahasiswa
        $evaluasi = EvaluasiTengahKP::updateOrCreate(
            ['student_id' => (int)$studentId],
            [
                'lecturer_id' => $request->lecturer_id,
                'skor_progres' => $request->skor_progres,
                'catatan_umum' => $request->catatan_umum,
                'tanggal_evaluasi' => now(), 
            ]
        );

        return response()->json($evaluasi, 201);
    }

    /**
     * Mengambil hasil Evaluasi Tengah KP untuk mahasiswa.
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexEvaluasiTengah($studentId) {
        return response()->json(EvaluasiTengahKP::where('student_id', $studentId)->firstOrFail());
    }
}