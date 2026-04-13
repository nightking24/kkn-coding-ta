<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

public function login(Request $request)
{
    $user = User::where('username', $request->username)->first();

    if ($user && Hash::check($request->password, $user->password)) {

        session(['user' => $user]);

        if ($user->role == 'admin') {
            return redirect('/dashboard');
        } elseif ($user->role == 'peserta') {
            return redirect('/hasil-peserta');
        } elseif ($user->role == 'dpl') {
            return redirect('/hasil-dpl');
        } elseif ($user->role == 'apl') {
            return redirect('/hasil-apl');
        }
    }

    return back()->with('error', 'Login gagal');
}

    public function logout()
    {
        session()->forget('user');
        return redirect('/');
    }
}