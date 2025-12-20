<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;



use App\Http\Controllers\PendaftaranKPController;
use App\Http\Controllers\InformasiKPController;
use App\Http\Controllers\SyaratKPController;
use App\Http\Controllers\JadwalKPController;
use App\Http\Controllers\TemplateDokumenController;
use App\Http\Controllers\ProsedurKPController;
use App\Http\Controllers\VerifikasiAdministrasiController;
use App\Http\Controllers\SuratPengantarKPController;
use App\Http\Controllers\JadwalSeminarKpController;
use App\Http\Controllers\PenilaianKPController;
use App\Http\Controllers\LaporanKPController;
use App\Http\Controllers\PengajuanTempatKPController;
use App\Http\Controllers\EvaluasiKPController;




Route::post('/tentukan-pembimbing', [PembimbingController::class, 'tentukanPembimbing']);
Route::post('/buat-surat-tugas', [PembimbingController::class, 'buatSuratTugas']);
Route::get('/kirim-notifikasi/{id}', [PembimbingController::class, 'kirimNotifikasi']);
Route::get('/download-surat-tugas/{id}', [PembimbingController::class, 'downloadSuratTugas']);
Route::delete('/delete-surat-tugas/{id}', [PembimbingController::class, 'deleteSuratTugas']);
Route::put('/update-pembimbing/{id}', [PembimbingController::class, 'updatePembimbing']);

Route::get('jadwal-bimbingan', [KonsultasiController::class,'indexJadwal']);
Route::post('jadwal-bimbingan', [KonsultasiController::class,'storeJadwal']);

Route::get('forum', [KonsultasiController::class,'indexForum']);
Route::post('forum', [KonsultasiController::class,'storeForum']);
Route::post('forum/{id}/komentar', [KonsultasiController::class,'storeKomentar']);

Route::get('catatan-bimbingan/{studentId}', [KonsultasiController::class,'indexCatatan']);
Route::post('catatan-bimbingan', [KonsultasiController::class,'storeCatatan']);

Route::post('logbook', [LogbookController::class, 'storeLogbook']);
Route::get('logbook/{studentId}', [LogbookController::class, 'indexLogbook']);
Route::get('logbook/detail/{logbookId}', [LogbookController::class, 'showLogbook']);
Route::put('logbook/{logbookId}', [LogbookController::class, 'updateLogbook']);
Route::delete('logbook/{logbookId}', [LogbookController::class, 'destroyLogbook']);
Route::post('/logbook/{logbookId}', [LogbookController::class, 'updateLogbook']);

Route::post('/pendaftaran-kp', [PendaftaranKPController::class, 'daftar']);
Route::post('/pendaftaran-kp/{id}/upload', [PendaftaranKPController::class, 'uploadBerkas']);
Route::post('/pendaftaran-kp/{id}/validasi', [PendaftaranKPController::class, 'validasi']);
Route::get('/pendaftaran-kp/{id}', [PendaftaranKPController::class, 'detail']);
Route::get('/pendaftaran-kp', [PendaftaranKPController::class, 'daftarSemua']);
Route::put('/pendaftaran-kp/{id}', [PendaftaranKPController::class, 'update']);
Route::delete('/pendaftaran-kp/{id}', [PendaftaranKPController::class, 'delete']);
Route::post('/informasi-kp/syarat', [InformasiKPController::class, 'storeSyarat']);
Route::post('/informasi-kp/jadwal', [InformasiKPController::class, 'storeJadwal']);
Route::post('/informasi-kp/template', [InformasiKPController::class, 'storeTemplate']);
Route::post('/informasi-kp/prosedur', [InformasiKPController::class, 'storeProsedur']);
Route::post('/verifikasi-admin', [VerifikasiAdministrasiController::class, 'store']);
Route::get('/verifikasi-admin', [VerifikasiAdministrasiController::class, 'index']);
Route::get('/verifikasi-admin/{id}', [VerifikasiAdministrasiController::class, 'show']);
Route::post('/verifikasi-admin/{id}/cek-kelengkapan', [VerifikasiAdministrasiController::class, 'cekKelengkapan']);
Route::post('/verifikasi-admin/{id}/setujui', [VerifikasiAdministrasiController::class, 'setujui']);
Route::post('/verifikasi-admin/{id}/tolak', [VerifikasiAdministrasiController::class, 'tolak']);
Route::get('/verifikasi-admin/{id}/status-mahasiswa', [VerifikasiAdministrasiController::class, 'getStatusMahasiswa']);
Route::post('/surat-pengantar', [SuratPengantarKPController::class, 'store']);
Route::get('/surat-pengantar', [SuratPengantarKPController::class, 'index']);
Route::get('/surat-pengantar/{id}', [SuratPengantarKPController::class, 'show']);
Route::post('/surat-pengantar/{id}/buat-pdf', [SuratPengantarKPController::class, 'buatPDF']);
Route::post('/surat-pengantar/{id}/tandatangani', [SuratPengantarKPController::class, 'tandatangani']);
Route::post('/surat-pengantar/{id}/tolak', [SuratPengantarKPController::class, 'tolak']);
Route::get('/surat-pengantar/{id}/download', [SuratPengantarKPController::class, 'download']);
Route::get('/surat-pengantar/{id}/status', [SuratPengantarKPController::class, 'getStatus']);


Route::get('/jadwal-seminar-kp', [JadwalSeminarKpController::class, 'index']);
Route::post('/jadwal-seminar-kp', [JadwalSeminarKpController::class, 'store']);
Route::get('/jadwal-seminar-kp/{jadwalSeminarKp}', [JadwalSeminarKpController::class, 'show']);
Route::put('/jadwal-seminar-kp/{jadwalSeminarKp}', [JadwalSeminarKpController::class, 'update']);
Route::delete('/jadwal-seminar-kp/{jadwalSeminarKp}', [JadwalSeminarKpController::class, 'destroy']);
Route::get('/penilaian-kp', [PenilaianKPController::class, 'index']);
Route::post('/penilaian-kp', [PenilaianKPController::class, 'store']);
Route::get('/penilaian-kp/{id}', [PenilaianKPController::class, 'show']);
Route::put('/penilaian-kp/{id}', [PenilaianKPController::class, 'update']);
Route::delete('/penilaian-kp/{id}', [PenilaianKPController::class, 'destroy']);
Route::post('/laporan-kp/upload', [LaporanKPController::class, 'upload']);
Route::get('/laporan-kp', [LaporanKPController::class, 'index']);
Route::get('/laporan-kp/{id}', [LaporanKPController::class, 'show']);
Route::delete('/laporan-kp/{id}', [LaporanKPController::class, 'destroy']);
Route::put('/laporan-kp/{id}', [LaporanKPController::class, 'update']);



Route::prefix('pengajuan-tempat-kp')->group(function () {
  Route::post('/', [PengajuanTempatKPController::class, 'store']);
    Route::get('/', [PengajuanTempatKPController::class, 'index']);
    Route::get('/{id}', [PengajuanTempatKPController::class, 'show']);
    Route::post('/{id}/persetujuan-instansi', [PengajuanTempatKPController::class, 'persetujuanInstansi']);
    Route::post('/{id}/persetujuan-jurusan', [PengajuanTempatKPController::class, 'persetujuanJurusan']);
    Route::get('/{id}/status', [PengajuanTempatKPController::class, 'getStatus']);
    Route::post('/{id}/reset', [PengajuanTempatKPController::class, 'reset']);  
   
});

Route::get('validasi-logbook', [ValidasiController::class, 'indexValidasi']);       
Route::post('validasi-logbook', [ValidasiController::class, 'storeValidasi']);      
Route::get('validasi-logbook/{id}', [ValidasiController::class, 'showValidasi']);   
Route::put('validasi-logbook/{id}', [ValidasiController::class, 'updateValidasi']); 
Route::delete('validasi-logbook/{id}', [ValidasiController::class, 'destroyValidasi']); 

Route::get('evaluasi-kp', [EvaluasiKPController::class, 'index']);            
Route::post('evaluasi-kp', [EvaluasiKPController::class, 'store']);           
Route::get('evaluasi-kp/{id}', [EvaluasiKPController::class, 'show']);        
Route::put('evaluasi-kp/{id}', [EvaluasiKPController::class, 'update']);      
Route::delete('evaluasi-kp/{id}', [EvaluasiKPController::class, 'destroy']);  

Route::get('evaluasi-kp/logbook/{logbookId}', [EvaluasiKPController::class, 'byLogbook']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('logbook', function (Request $request) {
    return Logbook::where('student_id', $request->user()->id)->get();
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/jadwal-seminar-me', [JadwalSeminarKPController::class, 'mySchedule']);
});