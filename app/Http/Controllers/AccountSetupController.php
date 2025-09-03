<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountSetupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . Auth::id()],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        
        // Check if this is actually a temp account
        if (!$user->is_temp_account) {
            throw ValidationException::withMessages([
                'error' => 'Account is al ingesteld.'
            ]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_temp_account' => false,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account succesvol ingesteld!'
        ]);
    }
}
