<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class dashboardController extends Controller
{
    //
    public function index()
    {
        return view('frontend.dashboard');
    }
    public function login()
    {
        return view('frontend.login');
    }

    public function loginSubmit(Request $request)
    {
        // 1. Validate Form Input
        $request->validate([
            'email'    => 'required|email:rfc,dns',
            'password' => 'required',
        ]);

        // 2. Fetch User from DB
        $user = DB::table('users')->where('email', $request->email)->first();

        // 3. Verify Password & Initiate Session
        if ($user && Hash::check($request->password, $user->password)) {
            // Put user identifier in the session
            Session::put('user', $user->email);

            return redirect()->intended('/');
        }

        // Return back if mismatch found
        return redirect()->back()->withErrors(['email' => 'These credentials do not match our database records.']);
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
    public function register()
    {
        return view('frontend.register');
    }

    public function registerSubmit(Request $request)
    {
        // 1. Form Data Validate Karein
        $request->validate([
            'name'     => 'required|string|min:3|max:255',
            'email'    => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // 2. Database mein safe user insert karein
        DB::table('users')->insert([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            // 'phone'      => null,  // Ya '' agar string chahiye
            // 'address'    => null,  // Ya '' agar text chahiye
            'registered_at' => now(), // Aapke schema mein 'registered_at' tha, 'created_at' nahi
        ]);

        // 3. Success message ke sath login page par bhejin
        return redirect('/login')->with('success', 'Account created successfully! Please login.');
    }
}
