<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\Api\DashboardKpController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\JadwalSeminarKPController;
use App\Http\Controllers\PenilaianKPController;
use App\Http\Controllers\LaporanKPController;
use App\Models\PendaftaranKP;



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
})->name('dashboard'); 

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


Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
    ]);
})->name('l5-swagger.api');


Route::get('/api-docs.yaml', function () {
    $path = storage_path('api-docs/api-docs.yaml');

    if (!file_exists($path)) {
        abort(404, "Swagger YAML not found. Run: php artisan l5-swagger:generate");
    }

    return response()->file($path, [
        'Content-Type' => 'application/x-yaml'
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Semua bisa akses profil sendiri
    Route::get('/me', [AuthController::class, 'me']);

    // Hanya yang role-nya admin bisa akses ini
    Route::get('/admin/dashboard-data', [AdminController::class, 'index'])->middleware('can:is-admin');
});

// Form tentukan dosen pembimbing
Route::get('/admin/tentukanpembimbing', 
[PembimbingController::class, 'formTentukanPembimbing']
)->name('pembimbing.form');
Route::get('/pendaftaran-kp', function () {
    return view('mahasiswa.PendaftaranKP');
})->name('pendaftaran.kp');

Route::get('/pendaftaran-kp/tambah', function () {
    return view('mahasiswa.TambahPendaftaranKP'); 
})->name('pendaftaran.kp.tambah');

Route::get('/pendaftaran-kp/edit/{id}', function ($id) {
    $pendaftaran = PendaftaranKP::find($id);

    if (!$pendaftaran) {
        return redirect()->route('pendaftaran.kp')
                         ->with('error', "Data pendaftaran dengan ID $id tidak ditemukan!");
    }

    return view('mahasiswa.EditPendaftaranKP', ['pendaftaran' => $pendaftaran]);
})->name('pendaftaran.kp.edit');
