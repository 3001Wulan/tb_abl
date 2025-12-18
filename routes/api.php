<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ValidasiController;


use App\Http\Controllers\PendaftaranKPController;
use App\Http\Controllers\InformasiKPController;
use App\Http\Controllers\SyaratKPController;
use App\Http\Controllers\JadwalKPController;
use App\Http\Controllers\TemplateDokumenController;
use App\Http\Controllers\ProsedurKPController;
use App\Http\Controllers\VerifikasiAdministrasiController;
use App\Http\Controllers\SuratPengantarKPController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\PenilaianKPController;
use App\Http\Controllers\PengumpulanLaporanKPController;



Route::post('/tentukan-pembimbing', [PembimbingController::class, 'tentukanPembimbing']);
Route::post('/buat-surat-tugas', [PembimbingController::class, 'buatSuratTugas']);
Route::get('/kirim-notifikasi/{id}', [PembimbingController::class, 'kirimNotifikasi']);
Route::get('/download-surat-tugas/{id}', [PembimbingController::class, 'downloadSuratTugas']);
Route::delete('/delete-surat-tugas/{id}', [PembimbingController::class, 'deleteSuratTugas']);
Route::put('/update-pembimbing/{id}', [PembimbingController::class, 'updatePembimbing']);

Route::get('jadwal-bimbingan', [KonsultasiController::class,'indexJadwal']);
Route::post('jadwal-bimbingan', [KonsultasiController::class,'storeJadwal']);

// Forum Konsultasi
Route::get('forum', [KonsultasiController::class,'indexForum']);
Route::post('forum', [KonsultasiController::class,'storeForum']);
Route::post('forum/{id}/komentar', [KonsultasiController::class,'storeKomentar']);

// Catatan Bimbingan
Route::get('catatan-bimbingan/{studentId}', [KonsultasiController::class,'indexCatatan']);
Route::post('catatan-bimbingan', [KonsultasiController::class,'storeCatatan']);

Route::post('logbook', [LogbookController::class, 'storeLogbook']);
// GET: Mengambil semua Logbook siswa (untuk Tracking Progres KP)
Route::get('logbook/{studentId}', [LogbookController::class, 'indexLogbook']);

// 2. Endpoint untuk Dosen Pembimbing (Validasi)
// POST: Dosen memvalidasi Logbook tertentu (Disetujui/Ditolak)
Route::post('logbook/{logbookId}/validasi', [LogbookController::class, 'storeValidasi']);
// GET: Melihat status validasi untuk Logbook tertentu
Route::get('logbook/{logbookId}/validasi', [LogbookController::class, 'indexValidasi']);

Route::post('logbook/{logbookId}/validasi', [ValidasiController::class, 'storeValidasi']);
// GET: Melihat detail validasi Logbook
Route::get('logbook/{logbookId}/validasi', [ValidasiController::class, 'indexValidasi']);

// POST: Dosen menyimpan/memperbarui Evaluasi Tengah
Route::post('evaluasi-tengah/{studentId}', [ValidasiController::class, 'storeEvaluasiTengah']);
// GET: Mengambil hasil Evaluasi Tengah
Route::get('evaluasi-tengah/{studentId}', [ValidasiController::class, 'indexEvaluasiTengah']);


Route::post('logbook', [LogbookController::class, 'storeLogbook']);

// 2. TRACKING PROGRES (GET)
// Digunakan oleh Siswa untuk melihat semua logbooknya.
Route::get('logbook/{studentId}', [LogbookController::class, 'indexLogbook']);

// 3. DETAIL LOGBOOK (GET)
// Digunakan oleh Siswa/Dosen untuk melihat detail satu logbook spesifik.
Route::get('logbook/detail/{logbookId}', [LogbookController::class, 'showLogbook']);

// 4. UPDATE LOGBOOK (PUT)
// Digunakan oleh Siswa untuk mengupdate logbook yang masih 'Pending'.
Route::put('logbook/{logbookId}', [LogbookController::class, 'updateLogbook']);

// 5. HAPUS LOGBOOK (DELETE)
// Digunakan oleh Siswa untuk menghapus logbook yang masih 'Pending'.
Route::delete('logbook/{logbookId}', [LogbookController::class, 'destroyLogbook']);

// 6. GLOBAL MONITORING (GET)
// Digunakan oleh Dosen untuk melihat semua logbook dari seluruh mahasiswa.
Route::get('logbook/all', [LogbookController::class, 'indexAllLogbooks']);

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


Route::apiResource('seminars', SeminarController::class);
Route::post('seminars/{seminar}/assign-examiners', [SeminarController::class, 'assignExaminers']);
Route::post('seminars/{seminar}/notify', [SeminarController::class, 'notify']);
Route::get('/penilaian', [PenilaianKPController::class, 'index']);
Route::post('/penilaian', [PenilaianKPController::class, 'store']);
Route::get('/penilaian/{id}', [PenilaianKPController::class, 'show']);
Route::put('/penilaian/{id}', [PenilaianKPController::class, 'update']);
Route::delete('/penilaian/{id}', [PenilaianKPController::class, 'destroy']);
Route::post('/laporan/upload', [PengumpulanLaporanKPController::class, 'upload']);
Route::get('/laporan', [PengumpulanLaporanKPController::class, 'index']);
Route::get('/laporan/{id}', [PengumpulanLaporanKPController::class, 'show']);
Route::delete('/laporan/{id}', [PengumpulanLaporanKPController::class, 'destroy']);
Route::post('/laporan/update/{id}', [PengumpulanLaporanKPController::class, 'update']);


