<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembimbingController;

Route::post('/tentukan-pembimbing', [PembimbingController::class, 'tentukanPembimbing']);
Route::post('/buat-surat-tugas', [PembimbingController::class, 'buatSuratTugas']);
Route::get('/kirim-notifikasi/{id}', [PembimbingController::class, 'kirimNotifikasi']);
