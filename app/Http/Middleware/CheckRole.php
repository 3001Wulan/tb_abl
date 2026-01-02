<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // JIKA ROLE TIDAK SESUAI, LANGSUNG LEMPAR KE DASHBOARD YANG BENAR
        if ($user->role !== $role) {
            if ($user->role === 'dosen') {
                return redirect('/dosen/dashboard');
            } elseif ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            } else {
                return redirect('/login');
            }
        }

        return $next($request);
    }
}