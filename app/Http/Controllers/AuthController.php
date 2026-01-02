<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Authentication"},
     * summary="Login User",
     * description="Gunakan email dan password untuk mendapatkan token.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login Berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Login berhasil"),
     * @OA\Property(property="token", type="string", example="1|abc123token"),
     * @OA\Property(property="user", type="object")
     * )
     * ),
     * @OA\Response(response=401, description="Kredensial tidak valid")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('ABL-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'token' => $token
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/register",
     * tags={"Authentication"},
     * summary="Register Baru",
     * description="Mendaftarkan user sekaligus membuat record Student dengan ID yang identik.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password","nim"},
     * @OA\Property(property="name", type="string", example="Lann"),
     * @OA\Property(property="email", type="string", format="email", example="lann@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123"),
     * @OA\Property(property="nim", type="string", example="2024001")
     * )
     * ),
     * @OA\Response(response=201, description="User dan Student berhasil dibuat")
     * )
     */
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $student = new Student();
        $student->id = $user->id; 
        $student->nama = $user->name;
        $student->email = $user->email;
        $student->nim = $request->nim;
        $student->save();

        $token = $user->createToken('ABL-Token')->plainTextToken;

        return response()->json([
            'message' => 'Register berhasil, ID: '.$user->id,
            'token' => $token
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/me",
     * tags={"Authentication"},
     * summary="Cek Profil",
     * description="Melihat data user yang sedang login beserta data student-nya.",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="id", type="integer", example=14),
     * @OA\Property(property="name", type="string", example="Lann"),
     * @OA\Property(property="student", type="object")
     * )
     * )
     * )
     */
    public function me(Request $request)
{
    $user = $request->user()->load('student');

    return response()->json($user);
}
}