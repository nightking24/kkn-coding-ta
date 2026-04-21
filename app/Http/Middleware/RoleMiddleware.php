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
        if (!session('user')) {
            return redirect('/');
        }

        $userRole = trim(strtolower(session('user')->role ?? ''));
        $requiredRole = trim(strtolower($role));

        if ($userRole != $requiredRole) {
            abort(403);
        }

        return $next($request);
    }
}
