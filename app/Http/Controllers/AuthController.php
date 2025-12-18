<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('ABL-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                // 'role' => $user->role (aktifkan kalau sudah ada)
            ],
            'token' => $token
        ], 200);
    }

    public function me(Request $request)
    {
        // Mengambil user login + relasi student
        $user = $request->user()->load('student');

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'student' => $user->student
        ]);
    }

    public function register(Request $request)
{
    // ... validasi tetap sama ...

    // 1. Buat User (Misal dapat ID 14)
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Hash::make($request->password),
    ]);

    // 2. Buat Student dengan ID yang diambil langsung dari $user->id
    $student = new \App\Models\Student();
    $student->id = $user->id; // Paksa ID sama dengan User (ID 14)
    $student->nama = $user->name;
    $student->email = $user->email;
    $student->nim = $request->nim;
    $student->save(); // Simpan manual agar ID tidak berubah

    $token = $user->createToken('ABL-Token')->plainTextToken;

    return response()->json([
        'message' => 'Register berhasil, ID User dan Student sekarang SAMA (ID: '.$user->id.')',
        'token' => $token
    ], 201);
}
}
