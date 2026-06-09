<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Menampilkan halaman login pengguna
    public function showLogin()
    {
        return view('login');
    }

    // Melakukan proses autentikasi pengguna berdasarkan username dan password
    public function login(Request $request)
    {
        // Mencari data pengguna berdasarkan username yang diinputkan
        $user = DB::table('users')
            ->where('username', $request->username)
            ->first();

        // Memastikan username ditemukan di database
        if (!$user) {
            return back()->with('error', 'Username salah');
        }

        // Memastikan password yang diinput sesuai dengan password yang tersimpan
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah');
        }

        // Menyimpan data pengguna ke dalam session setelah login berhasil
        session(['user' => $user]);

        // Mengambil role pengguna dan mengubahnya menjadi huruf kecil
        $role = trim(strtolower($user->role ?? ''));

        // Mengarahkan pengguna ke halaman sesuai hak aksesnya
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

    // Menghapus session pengguna dan keluar dari sistem
    public function logout()
    {
        // Menghapus data user dari session
        session()->forget('user');

        // Mengarahkan pengguna kembali ke halaman login
        return redirect('/');
    }
}