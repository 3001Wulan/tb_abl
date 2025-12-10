<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger UI
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
        'documentationTitle' => 'API Documentation'
    ]);
})->name('l5-swagger.default.api');

// Swagger JSON
Route::get('/api-docs.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (!file_exists($path)) {
        abort(404, "Swagger JSON not found. Run: php artisan l5-swagger:generate");
    }

    return response()->file($path);
});
