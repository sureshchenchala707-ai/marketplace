<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()
            ->addresses()
            ->latest()
            ->paginate(10);

        return view('customer.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('customer.addresses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'regex:/^[0-9]{6}$/'],
            'country' => ['required', 'string', 'max:100'],
            'address_type' => ['required', 'in:home,work,other'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($user, $validated) {
            $makeDefault = !empty($validated['is_default'])
                || !$user->addresses()->exists();

            if ($makeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $validated['is_default'] = $makeDefault;

            $user->addresses()->create($validated);
        });

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Address added successfully.');
    }

    public function edit(CustomerAddress $address)
    {
        $this->authorizeAddress($address);

        return view('customer.addresses.edit', compact('address'));
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($address);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'regex:/^[0-9]{6}$/'],
            'country' => ['required', 'string', 'max:100'],
            'address_type' => ['required', 'in:home,work,other'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($address, $validated) {
            $makeDefault = !empty($validated['is_default']);

            if ($makeDefault) {
                $address->user->addresses()
                    ->update(['is_default' => false]);
            }

            $validated['is_default'] = $makeDefault
                ? true
                : $address->is_default;

            $address->update($validated);
        });

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy(CustomerAddress $address)
    {
        $this->authorizeAddress($address);

        DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $user = $address->user;

            $address->delete();

            if ($wasDefault) {
                $nextAddress = $user->addresses()->oldest()->first();

                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Address deleted successfully.');
    }

    public function setDefault(CustomerAddress $address)
    {
        $this->authorizeAddress($address);

        DB::transaction(function () use ($address) {
            $address->user->addresses()
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Default address updated.');
    }

    private function authorizeAddress(CustomerAddress $address): void
    {
        abort_unless(
            $address->user_id === auth()->id(),
            403
        );
    }
}
