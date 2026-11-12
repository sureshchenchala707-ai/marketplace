<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class VendorRegistrationController extends Controller
{
    public function showRegister()
    {
        return view('auth.vendor.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],

            'shop_name' => ['required', 'string', 'max:255'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_phone' => ['required', 'string', 'max:20'],
            'gst_number' => ['nullable', 'string', 'max:20'],
            'pan_number' => ['nullable', 'string', 'max:10'],
            'business_address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:10'],
            'terms' => ['accepted'],
        ]);

        $vendorUser = DB::transaction(function () use ($validated) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->assignRole('vendor');

            $slug = Str::slug($validated['shop_name']);

            $uniqueSlug = $slug;
            $counter = 1;

            while (\App\Models\Vendor::where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $slug . '-' . $counter++;
            }

            $user->vendor()->create([
                'shop_name' => $validated['shop_name'],
                'slug' => $uniqueSlug,
                'business_email' => $validated['business_email'] ?? null,
                'business_phone' => $validated['business_phone'],
                'gst_number' => $validated['gst_number'] ?? null,
                'pan_number' => $validated['pan_number'] ?? null,
                'business_address' => $validated['business_address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'pincode' => $validated['pincode'],
                'status' => 'pending',
            ]);

            return $user;
        });

        return redirect()
            ->route('vendor.login')
            ->with('success', 'Your vendor application has been submitted. Please wait for admin approval.');
    }
}