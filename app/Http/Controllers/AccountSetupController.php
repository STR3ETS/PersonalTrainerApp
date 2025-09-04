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
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        
        if (!$user->is_temp_account) {
            return response()->json(['success' => false, 'message' => 'Account is al ingesteld.']);
        }

        $user->update([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_temp_account' => false,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account succesvol ingesteld!'
        ]);
    }
}
