<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class dashboardController extends Controller
{
    public function index(): View
    {
        return view('frontend.dashboard');
    }

    public function login(): View
    {
        return view('frontend.login');
    }

    public function loginSubmit(LoginRequest $request): RedirectResponse
    {
        // Native framework Auth attempts authentication, checks hashes, and generates session cookies
        if (Auth::attempt($request->validated())) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return redirect()->back()->withErrors([
            'email' => 'These credentials do not match our database records.',
        ]);
    }

    public function register(): View
    {
        return view('frontend.register');
    }

    public function registerSubmit(RegisterRequest $request): RedirectResponse
    {
        // Leverages Eloquent ORM. The Model auto-hashes the password on creation.
        User::create($request->validated());

        return redirect('/login')->with('success', 'Account created successfully! Please login.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Standard framework session flushing to secure application state
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}