<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Loginpagina tonen
    public function showLoginForm()
    {
        return view('welcome');
    }

    // Inloggen
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        // Proberen inloggen
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('/app');
        }

        // Mislukt
        return back()->withErrors([
            'email' => '* Ongeldige gegevens',
        ])->onlyInput('email');
    }

    // Uitloggen
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
