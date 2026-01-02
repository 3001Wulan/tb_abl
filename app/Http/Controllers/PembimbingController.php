<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supervision;
use App\Models\SuratTugas;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\PendaftaranKp;

/**
 * @OA\Tag(
 * name="Pembimbingan",
 * description="Operasi terkait penentuan, pembaruan dosen pembimbing, dan notifikasi."
 * )
 * @OA\Tag(
 * name="Surat Tugas",
 * description="Operasi terkait pembuatan, pengunduhan, dan penghapusan dokumen Surat Tugas."
 * )
 */
class PembimbingController extends Controller
{
   
    /**
     * @OA\Post(
     * path="/pembimbing",
     * operationId="tentukanPembimbing",
     * tags={"Pembimbingan"},
     * summary="Menentukan dosen pembimbing baru.",
     * description="Mencatat penunjukan dosen pembimbing beserta judul tugas untuk mahasiswa.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"student_id", "lecturer_id", "judul"},
     * @OA\Property(property="student_id", type="integer", example=1, description="ID Mahasiswa yang akan dibimbing."),
     * @OA\Property(property="lecturer_id", type="integer", example=12, description="ID Dosen pembimbing."),
     * @OA\Property(property="judul", type="string", example="Rancangan Sistem Informasi Akademik", description="Judul tugas yang akan dibimbing.")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Dosen pembimbing berhasil ditentukan",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Dosen pembimbing berhasil ditentukan"),
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validasi gagal"
     * )
     * )
     */
    public function tentukanPembimbing(Request $request)
{
    $request->validate([
        'student_id'  => 'required|exists:students,id',
        'lecturer_id' => 'required|exists:lecturers,id',
        'judul'       => 'required|string'
    ]);

    $existing = Supervision::where('student_id', $request->student_id)->first();

    if ($existing) {
        return response()->json([
            'message' => 'Mahasiswa sudah memiliki dosen pembimbing'
        ], 409);
    }

    $supervision = Supervision::create([
        'student_id'  => $request->student_id,
        'lecturer_id' => $request->lecturer_id,
        'judul'       => $request->judul
    ]);

    return response()->json([
        'message' => 'Dosen pembimbing berhasil ditentukan',
        'data'    => $supervision
    ], 201);
}

    
    /**
     * @OA\Post(
     * path="/surat-tugas",
     * operationId="buatSuratTugas",
     * tags={"Surat Tugas"},
     * summary="Membuat dan menyimpan surat tugas pembimbing.",
     * description="Membuat file surat tugas (simulasi .txt) dan mencatatnya ke database.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"supervision_id"},
     * @OA\Property(property="supervision_id", type="integer", example=1, description="ID data Supervision yang akan dibuatkan surat tugas.")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Surat tugas pembimbing berhasil dibuat",
     * ),
     * @OA\Response(
     * response=404,
     * description="Data pembimbing tidak ditemukan"
     * )
     * )
     */
    public function buatSuratTugas(Request $request)
    {
        $request->validate([
            'supervision_id' => 'required|exists:supervisions,id'
        ]);

        $supervision = Supervision::with(['student', 'lecturer'])->find($request->supervision_id);

        if (!$supervision) {
            return response()->json([
                'message' => 'Data pembimbing tidak ditemukan'
            ], 404);
        }

        $nomorSurat = "ST-" . strtoupper(Str::random(6));
        $folder = 'surat_tugas';

        if (!Storage::disk('local')->exists($folder)) {
            Storage::disk('local')->makeDirectory($folder);
        }

        $fileName = $nomorSurat . '.txt';
        $filePath = $folder . '/' . $fileName;

        $tanggal = date('d-m-Y');
        $content = "Nomor Surat: {$nomorSurat}\n" .
                    "Tanggal: {$tanggal}\n" .
                    "Kepada: {$supervision->lecturer->name}\n" .
                    "Mahasiswa: {$supervision->student->name}\n" .
                    "Judul Tugas: {$supervision->judul}\n\n" .
                    "Dengan ini ditugaskan sebagai dosen pembimbing mahasiswa di atas.";

        Storage::disk('local')->put($filePath, $content);

        $surat = SuratTugas::create([
            'supervision_id' => $supervision->id,
            'nomor_surat'    => $nomorSurat,
            'file_path'      => $filePath,
        ]);

        return response()->json([
            'message' => 'Surat tugas pembimbing berhasil dibuat',
            'data'    => $surat
        ], 201);
    }

    
    /**
     * @OA\Get(
     * path="/surat-tugas/{id}",
     * operationId="downloadSuratTugas",
     * tags={"Surat Tugas"},
     * summary="Mengunduh file surat tugas.",
     * description="Mengunduh file surat tugas yang tersimpan di storage lokal berdasarkan ID Surat Tugas.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID SuratTugas yang akan diunduh.",
     * @OA\Schema(type="integer", format="int64", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Berhasil mengunduh file.",
     * @OA\MediaType(
     * mediaType="application/octet-stream"
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="File surat tugas tidak ditemukan"
     * )
     * )
     */
    public function downloadSuratTugas($id)
    {
        $surat = SuratTugas::find($id);
    
        if (!$surat) {
            return response()->json([
                'message' => 'Data surat tidak ditemukan di database'
            ], 404);
        }
    
        if (!Storage::disk('local')->exists($surat->file_path)) {
            return response()->json([
                'message' => 'File fisik tidak ditemukan di folder storage/app/' . $surat->file_path
            ], 404);
        }
    
        return Storage::disk('local')->download($surat->file_path, $surat->nomor_surat . '.txt');
    }

    /**
     * @OA\Delete(
     * path="/surat-tugas/{id}",
     * operationId="deleteSuratTugas",
     * tags={"Surat Tugas"},
     * summary="Menghapus surat tugas dan file terkait.",
     * description="Menghapus record SuratTugas dari database dan file dari storage.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID SuratTugas yang akan dihapus.",
     * @OA\Schema(type="integer", format="int64", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Surat tugas pembimbing berhasil dihapus",
     * ),
     * @OA\Response(
     * response=404,
     * description="Surat tugas pembimbing tidak ditemukan"
     * )
     * )
     */
    public function deleteSuratTugas($id)
    {
        $surat = SuratTugas::find($id);

        if (!$surat) {
            return response()->json([
                'message' => 'Surat tugas pembimbing tidak ditemukan'
            ], 404);
        }

        if (Storage::disk('local')->exists($surat->file_path)) {
            Storage::disk('local')->delete($surat->file_path);
        }

        $surat->delete();

        return response()->json([
            'message' => 'Surat tugas pembimbing berhasil dihapus'
        ], 200);
    }


    /**
     * @OA\Put(
     * path="/pembimbing/{id}",
     * operationId="updatePembimbing",
     * tags={"Pembimbingan"},
     * summary="Memperbarui dosen pembimbing dan/atau judul tugas.",
     * description="Memperbarui data pembimbing (Supervision), menghapus surat tugas lama, dan membuat yang baru secara otomatis.",
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID data Supervision yang akan diperbarui.",
     * @OA\Schema(type="integer", format="int64", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"lecturer_id"},
     * @OA\Property(property="lecturer_id", type="integer", example=15, description="ID Dosen pembimbing yang baru."),
     * @OA\Property(property="judul", type="string", example="Perancangan Ulang Sistem Informasi", description="Judul tugas yang diperbarui (optional).")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Dosen pembimbing berhasil diperbarui dan surat tugas baru dibuat",
     * ),
     * @OA\Response(
     * response=404,
     * description="Data pembimbing tidak ditemukan"
     * )
     * )
     */
    public function updatePembimbing(Request $request, $id)
    {
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'judul'       => 'sometimes|string'
        ]);

        $supervision = Supervision::with(['student', 'lecturer'])->find($id);

        if (!$supervision) {
            return response()->json([
                'message' => 'Data pembimbing tidak ditemukan'
            ], 404);
        }

        $supervision->lecturer_id = $request->lecturer_id;

        if ($request->has('judul')) {
            $supervision->judul = $request->judul;
        }

        $supervision->save();

        $oldSurat = SuratTugas::where('supervision_id', $supervision->id)->first();
        if ($oldSurat && Storage::disk('local')->exists($oldSurat->file_path)) {
            Storage::disk('local')->delete($oldSurat->file_path);
            $oldSurat->delete();
        }

        $nomorSurat = "ST-" . strtoupper(Str::random(6));
        $folder = 'surat_tugas';
        if (!Storage::disk('local')->exists($folder)) {
            Storage::disk('local')->makeDirectory($folder);
        }
        $fileName = $nomorSurat . '.txt';
        $filePath = $folder . '/' . $fileName;

        $tanggal = date('d-m-Y');
        $content = "Nomor Surat: {$nomorSurat}\n" .
                    "Tanggal: {$tanggal}\n" .
                    "Kepada: {$supervision->lecturer->name}\n" .
                    "Mahasiswa: {$supervision->student->name}\n" .
                    "Judul Tugas: {$supervision->judul}\n\n" .
                    "Dengan ini ditugaskan sebagai dosen pembimbing mahasiswa di atas.";

        Storage::disk('local')->put($filePath, $content);

        $surat = SuratTugas::create([
            'supervision_id' => $supervision->id,
            'nomor_surat'    => $nomorSurat,
            'file_path'      => $filePath,
        ]);

        return response()->json([
            'message' => 'Dosen pembimbing berhasil diperbarui dan surat tugas baru dibuat',
            'data'    => [
                'supervision' => $supervision,
                'surat_tugas' => $surat
            ]
        ], 200);
    }
    public function formTentukanPembimbing(Request $request)
    {
        $lecturers = Lecturer::all();

        $pendaftaranKP = PendaftaranKp::with('student')->get();

        return view('admin.tentukanpembimbing', compact('lecturers', 'pendaftaranKP'));
    }
public function showStudent($id)
{
    try {
        $student = \App\Models\Student::findOrFail($id);
        return response()->json($student);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Mahasiswa tidak ditemukan'], 404);
    }
}
}