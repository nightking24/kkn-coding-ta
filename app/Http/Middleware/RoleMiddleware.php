<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        // Mengecek apakah pengguna sudah login
        if (!session('user')) {

            // Jika belum login, kembali ke halaman login
            return redirect('/');
        }

        // Mengambil role pengguna yang sedang login
        $userRole = trim(strtolower(session('user')->role ?? ''));

        // Mengambil role yang diizinkan pada route
        $requiredRole = trim(strtolower($role));

        // Jika role pengguna tidak sesuai
        if ($userRole != $requiredRole) {

            // Menampilkan halaman Forbidden (403)
            abort(403);
        }

        // Jika role sesuai, lanjut ke controller
        return $next($request);
    }
}
