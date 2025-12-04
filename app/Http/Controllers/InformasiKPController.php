<?php

namespace App\Http\Controllers;

use App\Models\InformasiKP;
use Illuminate\Http\Request;

class InformasiKPController extends Controller
{
    // ==================== STORE METHODS ====================

    /**
     * POST - Tambah Syarat KP
     * POST /api/informasi-kp/syarat
     */
    public function storeSyarat(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'nullable',
            'is_wajib' => 'nullable|boolean'
        ]);

        $syarat = InformasiKP::create([
            'jenis_informasi' => 'syarat',
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'is_wajib' => $request->is_wajib ?? false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Syarat KP berhasil ditambahkan',
            'data' => $syarat
        ], 201);
    }

    /**
     * POST - Tambah Jadwal KP
     * POST /api/informasi-kp/jadwal
     */
    public function storeJadwal(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'periode' => 'nullable',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'deskripsi' => 'nullable'
        ]);

        $jadwal = InformasiKP::create([
            'jenis_informasi' => 'jadwal',
            'judul' => $request->judul,
            'periode' => $request->periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal KP berhasil ditambahkan',
            'data' => $jadwal
        ], 201);
    }

    /**
     * POST - Tambah Template Dokumen
     * POST /api/informasi-kp/template
     */
  public function storeTemplate(Request $request)
{
    $request->validate([
        'judul' => 'required'
    ]);

    $template = InformasiKP::create([
        'jenis_informasi' => 'template',
        'judul' => $request->judul,
        'jenis_dokumen' => $request->jenis_dokumen,
        'file_path' => $request->file_path,
        'deskripsi' => $request->deskripsi
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Template dokumen berhasil ditambahkan',
        'data' => $template
    ], 201);
}
    /**
     * POST - Tambah Prosedur KP
     * POST /api/informasi-kp/prosedur
     */
    public function storeProsedur(Request $request)
    {
        $request->validate([
            'urutan' => 'required|integer',
            'judul' => 'required',
            'deskripsi' => 'nullable',
            'konten' => 'nullable'
        ]);

        $prosedur = InformasiKP::create([
            'jenis_informasi' => 'prosedur',
            'urutan' => $request->urutan,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'konten' => $request->konten
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Prosedur KP berhasil ditambahkan',
            'data' => $prosedur
        ], 201);
    }
}