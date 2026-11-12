<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopProfileController extends Controller
{
    public function edit()
    {
        $vendor = auth()->user()->vendor;

        abort_unless($vendor, 404, 'Vendor profile not found.');

        return view('vendor.profile.edit', compact('vendor'));
    }

    public function update(Request $request)
    {
        $vendor = auth()->user()->vendor;

        abort_unless($vendor, 404, 'Vendor profile not found.');

        $validated = $request->validate([
            'shop_name' => [
                'required', 'string', 'max:255',
                Rule::unique('vendors', 'shop_name')->ignore($vendor->id),
            ],
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'required|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|size:10',
            'business_address' => 'required|string|max:2000',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',

            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        unset($validated['logo'], $validated['banner']);

        $validated['gst_number'] = $validated['gst_number'] ?? null;
        $validated['pan_number'] = $validated['pan_number']
            ? strtoupper($validated['pan_number'])
            : null;

        // Preserve the existing shop URL.
        // Changing a slug can break existing product/store links.

        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }

            $validated['logo'] = $request->file('logo')
                ->store('vendors/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($vendor->banner) {
                Storage::disk('public')->delete($vendor->banner);
            }

            $validated['banner'] = $request->file('banner')
                ->store('vendors/banners', 'public');
        }

        $vendor->update($validated);

        return back()->with('success', 'Shop profile updated successfully!');
    }
}
