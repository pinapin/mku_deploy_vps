<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Http\Middleware\CheckSession;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if(Session::has('kode')){
            return redirect()->route('dashboard');
        }

        // Menggunakan fungsi dari CheckSession untuk menangani SSO token
        // if (isset($_COOKIE['sso_token'])) {
        //     $checkSession = new CheckSession();
        //     $result = $checkSession->handleSSOToken($_COOKIE['sso_token']);
            
        //     // Jika berhasil login via SSO, redirect ke dashboard
        //     if ($result) {
        //         return redirect()->route('dashboard');
        //     }
        // }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('username', 'password');
        $remember = $request->has('remember') ? true : false;

        // Coba login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            Session::put('kode', Auth::user()->username);

            $redirectRoute = 'dashboard';

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => route($redirectRoute)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah',
            'errors' => [
                'credentials' => ['Username atau password salah']
            ]
        ], 401);
    }

    public function logout(Request $request)
    {
        // Auth::logout();

        setcookie('sso_token', '', time() - 3600, '/', '.umk.ac.id');
        setcookie('sso_token', '', time() - 3600, '/');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
