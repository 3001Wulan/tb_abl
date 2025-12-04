<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LogbookKP;
use App\Models\ValidasiLogbook; // Diperlukan untuk relasi 'with'
use App\Models\EvaluasiTengahKP; // Diperlukan untuk relasi di Model Logbook (jika ada)

class LogbookController extends Controller {

    // ==== Layanan 3: Logbook / Monitoring KP (CRUD & Tracking oleh Siswa) ====
    
    /**
     * Siswa mengunggah Logbook kegiatan mingguan.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLogbook(Request $request) {
        // Penambahan validasi unik minggu_ke per student untuk mencegah duplikasi
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'minggu_ke' => 'required|integer|unique:logbook_k_p_s,minggu_ke,NULL,id,student_id,' . $request->student_id, 
            'tanggal_mulai' => 'required|date',
            'deskripsi_kegiatan' => 'required',
        ]);
        
        $logbook = LogbookKP::create($request->all());
        return response()->json($logbook, 201);
    }

    /**
     * Mengambil semua Logbook (beserta validasinya) untuk seorang siswa. (Tracking Progres KP)
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexLogbook($studentId) {
        return response()->json(LogbookKP::where('student_id', $studentId)->with('validasi')->get());
    }

    /**
     * Menampilkan detail satu Logbook berdasarkan ID-nya. (CRUD Read Detail)
     * @param int $logbookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showLogbook($logbookId) {
        $logbook = LogbookKP::with('validasi')->findOrFail($logbookId);
        return response()->json($logbook);
    }

    /**
     * Siswa mengupdate Logbook yang belum divalidasi. (CRUD Update)
     * @param Request $request
     * @param int $logbookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLogbook(Request $request, $logbookId) {
        $logbook = LogbookKP::findOrFail($logbookId);

        // Pencegahan: Logbook yang sudah divalidasi tidak boleh diubah
        if ($logbook->status !== 'Pending') {
            return response()->json(['message' => 'Logbook sudah divalidasi dan tidak dapat diubah.'], 403);
        }

        $request->validate([
            'minggu_ke' => 'required|integer|unique:logbook_k_p_s,minggu_ke,' . $logbookId . ',id,student_id,' . $logbook->student_id,
            'tanggal_mulai' => 'required|date',
            'deskripsi_kegiatan' => 'required',
        ]);

        $logbook->update($request->all());
        return response()->json($logbook);
    }

    /**
     * Siswa menghapus Logbook yang belum divalidasi. (CRUD Delete)
     * @param int $logbookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyLogbook($logbookId) {
        $logbook = LogbookKP::findOrFail($logbookId);

        // Pencegahan: Logbook yang sudah divalidasi tidak boleh dihapus
        if ($logbook->status !== 'Pending') {
            return response()->json(['message' => 'Logbook sudah divalidasi dan tidak dapat dihapus.'], 403);
        }

        $logbook->delete();
        return response()->json(['message' => 'Logbook berhasil dihapus.'], 200);
    }

    /**
     * Dosen mengambil semua logbook dari semua siswa. (Global Monitoring View)
     * @param Request $request (Opsional untuk filtering)
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAllLogbooks(Request $request) {
        // Asumsi relasi 'student' ada di model LogbookKP
        $query = LogbookKP::with(['validasi', 'student']); 
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        return response()->json($query->orderBy('created_at', 'desc')->paginate(10));
    }

    // CATATAN PENTING:
    // Metode storeValidasi dan indexValidasi telah dipindahkan 
    // ke ValidasiController.php (Layanan 4).
}