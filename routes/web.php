<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogbookController;

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
})->name('register');

Route::get('/mahasiswa/logbook', function () {
    return view('mahasiswa.logbook');
})->name('logbook');

Route::get('/mahasiswa/logbook/create', [LogbookController::class, 'create'])
    ->name('logbook.create');

Route::get('/mahasiswa/logbook/{id}/edit', [LogbookController::class, 'edit'])
    ->name('logbook.edit');

Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
        'documentationTitle' => 'API Documentation'
    ]);
})->name('l5-swagger.default.api');

Route::get('/api-docs.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (!file_exists($path)) {
        abort(404, "Swagger JSON not found. Run: php artisan l5-swagger:generate");
    }

    return response()->file($path);
});

