<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Routing\Controller; // Pastikan kita menggunakan kelas dasar Controller

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Dokumentasi API Pembimbing",
 * description="API untuk manajemen data surat tugas dan pembimbing."
 * )
 *
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Server Utama"
 * )
 */
class SwaggerController extends Controller
{
    // Class ini hanya untuk menampung anotasi global.
    // Metode-metode API didokumentasikan di PembimbingController.php
}