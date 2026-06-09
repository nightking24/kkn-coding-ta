<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Jika sudah login, lanjutkan ke halaman yang dituju
        if (!session('user')) {

            // Jika belum login, arahkan ke halaman login
            return redirect('/');
        }

        // Jika sudah login, lanjutkan ke halaman yang dituju
        return $next($request);
    }
}
