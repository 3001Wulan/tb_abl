<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supervision;
use App\Models\SuratTugas;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PembimbingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. Menentukan Dosen Pembimbing
    |--------------------------------------------------------------------------
    */
    public function tentukanPembimbing(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'judul'       => 'required|string'
        ]);

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

    /*
    |--------------------------------------------------------------------------
    | 2. Membuat Surat Tugas Pembimbing
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | 3. Download Surat Tugas Pembimbing
    |--------------------------------------------------------------------------
    */
    public function downloadSuratTugas($id)
    {
        $surat = SuratTugas::find($id);

        if (!$surat || !Storage::disk('local')->exists($surat->file_path)) {
            return response()->json([
                'message' => 'File surat tugas tidak ditemukan'
            ], 404);
        }

        return Storage::disk('local')->download($surat->file_path);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Menghapus Surat Tugas Pembimbing
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | 5. Notifikasi ke Mahasiswa dan Dosen
    |--------------------------------------------------------------------------
    */
    public function kirimNotifikasi($supervisionId)
    {
        $supervision = Supervision::with(['student', 'lecturer'])->find($supervisionId);

        if (!$supervision) {
            return response()->json([
                'message' => 'Data pembimbing tidak ditemukan'
            ], 404);
        }

        $notif = [
            'pesan' => 'Penunjukan dosen pembimbing telah ditetapkan dan surat tugas telah diterbitkan.',
            'dikirim_ke_mahasiswa' => $supervision->student->email,
            'dikirim_ke_dosen'     => $supervision->lecturer->email,
        ];

        return response()->json([
            'message' => 'Notifikasi berhasil dikirim ke mahasiswa dan dosen',
            'data'    => $notif
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Update Dosen Pembimbing (dan buat ulang surat tugas jika diganti)
    |--------------------------------------------------------------------------
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

        // Hapus surat lama
        $oldSurat = SuratTugas::where('supervision_id', $supervision->id)->first();
        if ($oldSurat && Storage::disk('local')->exists($oldSurat->file_path)) {
            Storage::disk('local')->delete($oldSurat->file_path);
            $oldSurat->delete();
        }

        // Buat surat baru
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
}
