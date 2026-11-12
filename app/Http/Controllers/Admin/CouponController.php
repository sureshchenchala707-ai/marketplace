<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::with('creator')
            ->withCount('usages');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        $coupons = $query->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 'active')->count(),
            'inactive' => Coupon::where('status', 'inactive')->count(),
            'expired' => Coupon::where('expires_at', '<', now())->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);

        $productIds = $validated['product_ids'] ?? [];
        $categoryIds = $validated['category_ids'] ?? [];
        $vendorIds = $validated['vendor_ids'] ?? [];

        unset(
            $validated['product_ids'],
            $validated['category_ids'],
            $validated['vendor_ids']
        );

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['created_by'] = $request->user()->id;
        $validated['used_count'] = 0;

        DB::transaction(function () use (
            $validated,
            $productIds,
            $categoryIds,
            $vendorIds
        ) {
            $coupon = Coupon::create($validated);

            $coupon->products()->sync($productIds);
            $coupon->categories()->sync($categoryIds);
            $coupon->vendors()->sync($vendorIds);
        });

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        $coupon->load([
            'products',
            'categories',
            'vendors',
        ]);

        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $this->validateCoupon($request, $coupon);

        $productIds = $validated['product_ids'] ?? [];
        $categoryIds = $validated['category_ids'] ?? [];
        $vendorIds = $validated['vendor_ids'] ?? [];

        unset(
            $validated['product_ids'],
            $validated['category_ids'],
            $validated['vendor_ids'],
            $validated['used_count'],
            $validated['created_by']
        );

        $validated['code'] = strtoupper(trim($validated['code']));

        DB::transaction(function () use (
            $coupon,
            $validated,
            $productIds,
            $categoryIds,
            $vendorIds
        ) {
            $coupon->update($validated);

            $coupon->products()->sync($productIds);
            $coupon->categories()->sync($categoryIds);
            $coupon->vendors()->sync($vendorIds);
        });

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function toggleStatus(Coupon $coupon)
    {
        if ($coupon->status === 'expired') {
            return back()->with(
                'error',
                'Expired coupons cannot be activated. Update the expiry date first.'
            );
        }

        $coupon->update([
            'status' => $coupon->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        return back()->with(
            'success',
            'Coupon status updated successfully.'
        );
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->usages()->exists()) {
            return back()->with(
                'error',
                'This coupon has usage history and cannot be deleted. Deactivate it instead.'
            );
        }

        DB::transaction(function () use ($coupon) {
            $coupon->products()->detach();
            $coupon->categories()->detach();
            $coupon->vendors()->detach();

            $coupon->delete();
        });

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    private function validateCoupon(
        Request $request,
        ?Coupon $coupon = null
    ): array {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],

            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed', 'free_shipping']),
            ],

            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
                'exclude_if:discount_type,free_shipping',
            ],

            'maximum_discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'minimum_order_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'usage_limit_per_user' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'first_order_only' => ['nullable', 'boolean'],
            'is_stackable' => ['nullable', 'boolean'],

            'starts_at' => ['nullable', 'date'],

            'expires_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'expired']),
            ],

            // Coupon product targeting
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => [
                'integer',
                'distinct',
                'exists:products,id',
            ],

            // Coupon category targeting
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [
                'integer',
                'distinct',
                'exists:categories,id',
            ],

            // Coupon vendor targeting
            'vendor_ids' => ['nullable', 'array'],
            'vendor_ids.*' => [
                'integer',
                'distinct',
                'exists:vendors,id',
            ],
        ]);
    }
}
