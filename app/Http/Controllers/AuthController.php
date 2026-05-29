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
        if (!config('app.quick_login')) {
            $this->refreshCaptcha($request);
        }

        return view('auth.login', [
            'quickLogin' => config('app.quick_login'),
            'selectedRole' => $request->query('role'),
        ]);
    }

    public function showUserLogin(Request $request)
    {
        $request->merge(['role' => 'peserta']);

        return $this->showLogin($request);
    }

    public function showAdminLogin(Request $request)
    {
        $request->merge(['role' => 'admin']);

        return $this->showLogin($request);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string'],
            'role' => ['nullable', 'in:admin,peserta'],
        ]);

        if (strtoupper($credentials['captcha']) !== session('captcha_code')) {
            $this->refreshCaptcha($request);
            return back()->withErrors(['captcha' => 'Captcha tidak sesuai.'])->withInput();
        }

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (!empty($credentials['role']) && Auth::user()->role !== $credentials['role']) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $this->refreshCaptcha($request);

                return back()->withErrors(['login' => 'Akun tidak sesuai dengan pilihan login.'])->withInput();
            }

            return Auth::user()->isAdmin() ? redirect('/admin') : redirect('/dashboard');
        }

        $this->refreshCaptcha($request);
        return back()->withErrors(['login' => 'Username/email atau password salah.'])->withInput();
    }

    public function quickLogin(Request $request, string $role)
    {
        abort_unless(config('app.quick_login'), 404);
        abort_unless(in_array($role, ['admin', 'peserta'], true), 404);

        $user = User::where('role', $role)->firstOrFail();
        Auth::login($user);
        $request->session()->regenerate();

        return $user->isAdmin() ? redirect('/admin') : redirect('/dashboard');
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
            'position_name' => ['nullable', 'string', 'max:255'],
            'work_unit' => ['nullable', 'string', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:100'],
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
