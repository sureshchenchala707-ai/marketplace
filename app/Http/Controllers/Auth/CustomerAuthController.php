<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.customer.login');
    }

    // ```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    // Step 1: Verify credentials
    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    // Step 2: Check account status
    $user = Auth::user();

    if ($user->status !== 'active') {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'email' => 'Your account is inactive. Please contact support.',
        ]);
    }

    // Step 3: Regenerate session
    $request->session()->regenerate();

    // Step 4: Verify customer role
    if (!$request->user()->hasRole('customer')) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'email' => 'Please use the correct login portal.',
        ]);
    }

    // Step 5: Redirect customer
    return redirect()->intended(route('account.dashboard'));
}


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}