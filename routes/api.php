<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\SeminarController;


Route::post('/tentukan-pembimbing', [PembimbingController::class, 'tentukanPembimbing']);
Route::post('/buat-surat-tugas', [PembimbingController::class, 'buatSuratTugas']);
Route::get('/kirim-notifikasi/{id}', [PembimbingController::class, 'kirimNotifikasi']);

Route::apiResource('seminars', SeminarController::class);
Route::post('seminars/{seminar}/assign-examiners', [SeminarController::class, 'assignExaminers']);
Route::post('seminars/{seminar}/notify', [SeminarController::class, 'notify']);