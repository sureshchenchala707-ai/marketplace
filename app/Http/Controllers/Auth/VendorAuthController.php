<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VendorAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.vendor.login');
    }


public function login(Request $request)
{
    // Step 1: Validate credentials
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    // Step 2: Authenticate user
    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    // Step 3: Check account status
    $user = Auth::user();

    if ($user->status !== 'active') {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'email' => 'Your account is inactive. Please contact support.',
        ]);
    }

    // Step 4: Regenerate session
    $request->session()->regenerate();

    // Step 5: Verify vendor role
    if (!$request->user()->hasRole('vendor')) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'email' => 'You are not authorized to access the vendor panel.',
        ]);
    }

    // Step 6: Redirect vendor
    return redirect()->intended(route('vendor.dashboard'));
}


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login');
    }
}