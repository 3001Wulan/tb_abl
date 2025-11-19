<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supervision;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\SuratTugas;   // <-- WAJIB! (ini yang hilang)
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PembimbingController extends Controller
{
    // 1. Menentukan Pembimbing
    public function tentukanPembimbing(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'lecturer_id' => 'required',
            'judul' => 'required'
        ]);

        $supervision = Supervision::create([
            'student_id' => $request->student_id,
            'lecturer_id' => $request->lecturer_id,
            'judul' => $request->judul
        ]);

        return response()->json([
            'message' => 'Pembimbing berhasil ditentukan',
            'data' => $supervision
        ], 201);
    }

    // 2. Membuat Surat Tugas
    public function buatSuratTugas(Request $request)
    {
        $request->validate([
            'supervision_id' => 'required'
        ]);

        $nomor = "ST-" . strtoupper(Str::random(6));
        $filePath = "uploads/surat_tugas/" . $nomor . ".pdf";

        Storage::put($filePath, "Surat Tugas Pembimbing");

        $surat = SuratTugas::create([
            'supervision_id' => $request->supervision_id,
            'nomor_surat' => $nomor,
            'file_path' => $filePath,
        ]);

        return response()->json([
            'message' => 'Surat tugas berhasil dibuat',
            'data' => $surat
        ]);
    }

    // 3. Mengirim Notifikasi
    public function kirimNotifikasi($supervisionId)
    {
        $supervision = Supervision::with(['student', 'lecturer'])->findOrFail($supervisionId);

        $notif = [
            'to_student' => "Email dikirim ke: " . $supervision->student->email,
            'to_lecturer' => "Email dikirim ke: " . $supervision->lecturer->email,
            'message' => "Penunjukan pembimbing telah dibuat"
        ];

        return response()->json([
            'message' => 'Notifikasi terkirim',
            'data' => $notif
        ]);
    }
}
