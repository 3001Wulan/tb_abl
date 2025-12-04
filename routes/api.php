<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ValidasiController;


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