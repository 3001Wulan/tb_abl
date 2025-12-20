<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\JadwalSeminarKPController;
use App\Http\Controllers\PenilaianKPController;
use App\Http\Controllers\LaporanKPController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register'); 
})->name('register');


Route::get('/dashboard', function () {
    return view('mahasiswa.dashboard'); 
})->name('dashboard'); // ini salah tulis 'register', harusnya 'dashboard'

Route::get('/mahasiswa/logbook', function () {
    return view('mahasiswa.logbook');
})->name('logbook');

Route::get('/mahasiswa/logbook/create', [LogbookController::class, 'create'])
    ->name('logbook.create');

Route::get('/mahasiswa/logbook/{id}/edit', [LogbookController::class, 'edit'])
    ->name('logbook.edit');

    Route::get('/mahasiswa/jadwal-seminar', [JadwalSeminarKPController::class, 'showForMahasiswa'])
        ->name('mahasiswa.jadwal-seminar');

    Route::get('/mahasiswa/nilai-kp', [PenilaianKPController::class, 'showForMahasiswa'])
        ->name('mahasiswa.nilai-kp');
    
    Route::get('/mahasiswa/laporan-akhir', [LaporanKPController::class, 'showUploadPage'])
        ->name('mahasiswa.laporan-akhir');

    Route::get('/mahasiswa/laporan-akhir/upload', [LaporanKPController::class, 'showUploadForm'])
        ->name('mahasiswa.laporan-upload');

// HAPUS salah satu route duplikat, pakai yang ini saja:
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
    ]);
})->name('l5-swagger.api');

// Route untuk file YAML
Route::get('/api-docs.yaml', function () {
    $path = storage_path('api-docs/api-docs.yaml');

    if (!file_exists($path)) {
        abort(404, "Swagger YAML not found. Run: php artisan l5-swagger:generate");
    }

    return response()->file($path, [
        'Content-Type' => 'application/x-yaml'
    ]);
});
