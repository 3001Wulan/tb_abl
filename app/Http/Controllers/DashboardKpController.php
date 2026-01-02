<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CatatanBimbingan;

class DashboardKpController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role === 'admin') {
                $totalMahasiswa = DB::table('students')->count();
                $suratPending = DB::table('surat_pengantar_kp')
                    ->where('status_pengajuan', 'pending') 
                    ->count();

                $pendaftarTerbaru = DB::table('pendaftaran_kp')
                    ->select('id', 'nama_mahasiswa', 'perusahaan as instansi') 
                    ->latest()
                    ->take(5)
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'stats' => [
                            'total_mahasiswa' => $totalMahasiswa,
                            'surat_pending'   => $suratPending,
                        ],
                        'pendaftar_terbaru' => $pendaftarTerbaru
                    ]
                ], 200);
            }

            if ($user->role === 'mahasiswa') {
                $pendaftaran = DB::table('pendaftaran_kp')
                    ->where('email', $user->email)
                    ->first();
                $statusAdm = 'Belum Terdaftar';
                if ($pendaftaran) {
                    $verifikasi = DB::table('verifikasi_administrasi')
                        ->where('pendaftaran_kp_id', $pendaftaran->id)
                        ->first();
                    $statusAdm = $verifikasi->status_mahasiswa ?? 'Menunggu Verifikasi';
                }

                $student = DB::table('students')->where('email', $user->email)->first();
                $totalBimbingan = 0;
                $riwayatBimbingan = [];
                $dosenPlot = null;

                if ($student) {
                    $riwayat = CatatanBimbingan::with('lecturer')
                        ->where('student_id', $student->id)
                        ->get();
                    
                    $totalBimbingan = $riwayat->count();

                    $riwayatBimbingan = $riwayat->map(function($r) {
                        return [
                            'pembimbing' => $r->lecturer->nama ?? 'Dosen',
                            'topik' => substr($r->isi_catatan, 0, 50),
                            'tanggal' => $r->tanggal
                        ];
                    });

                    $supervisi = DB::table('supervisions')
                        ->join('lecturers', 'supervisions.lecturer_id', '=', 'lecturers.id')
                        ->where('supervisions.student_id', $student->id)
                        ->select('lecturers.nama')
                        ->first();
                    
                    $dosenPlot = $supervisi ? ['nama' => $supervisi->nama] : null;
                }

                $logbook = DB::table('logbook_k_p_s')
                    ->where('student_id', $student->id ?? 0)
                    ->latest()
                    ->first();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'status_surat' => $statusAdm,
                        'total_bimbingan' => $totalBimbingan,
                        'dosen_pembimbing' => $dosenPlot,
                        'riwayat_bimbingan' => $riwayatBimbingan,
                        'log_terbaru' => $logbook ? [
                            'minggu_ke' => $logbook->minggu_ke,
                            'deskripsi' => $logbook->deskripsi_kegiatan,
                            'tanggal' => $logbook->tanggal_mulai
                        ] : null
                    ]
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'Role tidak dikenali'], 403);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}