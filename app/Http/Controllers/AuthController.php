<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $this->refreshCaptcha($request);
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string'],
        ]);

        if (strtoupper($credentials['captcha']) !== session('captcha_code')) {
            $this->refreshCaptcha($request);
            return back()->withErrors(['captcha' => 'Captcha tidak sesuai.'])->withInput();
        }

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return Auth::user()->isAdmin() ? redirect('/admin') : redirect('/dashboard');
        }

        $this->refreshCaptcha($request);
        return back()->withErrors(['login' => 'Username/email atau password salah.'])->withInput();
    }

    public function refreshCaptcha(Request $request)
    {
        $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5));
        $request->session()->put('captcha_code', $code);

        if ($request->expectsJson()) {
            return response()->json(['code' => $code]);
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'alpha_dash', 'max:100', 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'phone' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:255'],
        ]);

        $data['role'] = 'peserta';
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        Auth::login($user);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
