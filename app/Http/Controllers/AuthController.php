<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $user = DB::table('users')
            ->where('username', $request->username)
            ->first();

        // Check if user exists
        if (!$user) {
            return back()->with('error', 'Username salah');
        }

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah');
        }

        // ✅ set session SETELAH valid
        session(['user' => $user]);

        $role = trim(strtolower($user->role ?? ''));

        if ($role == 'admin') {
            return redirect('/dashboard');
        } elseif ($role == 'peserta') {
            return redirect('/hasil-peserta');
        } elseif ($role == 'dpl') {
            return redirect('/dpl-view');
        } elseif ($role == 'apl') {
            return redirect('/hasil-apl-new');
        }
    }

    public function logout()
    {
        session()->forget('user');
        return redirect('/');
    }
}