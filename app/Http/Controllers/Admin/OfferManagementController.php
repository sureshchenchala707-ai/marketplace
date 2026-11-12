<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OfferManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Offer::with('creator')
            ->withCount('products', 'categories', 'vendors');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        $offers = $query->orderByDesc('priority')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Offer::count(),
            'active' => Offer::where('status', 'active')->count(),
            'inactive' => Offer::where('status', 'inactive')->count(),
            'expired' => Offer::where('expires_at', '<', now())->count(),
        ];

        return view('admin.offers.index', compact('offers', 'stats'));
    }

    public function create()
    {
        return view('admin.offers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateOffer($request);

        $productIds = $validated['product_ids'] ?? [];
        $categoryIds = $validated['category_ids'] ?? [];
        $vendorIds = $validated['vendor_ids'] ?? [];

        unset(
            $validated['product_ids'],
            $validated['category_ids'],
            $validated['vendor_ids']
        );

        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['created_by'] = $request->user()->id;
        $validated['used_count'] = 0;

        DB::transaction(function () use (
            $validated,
            $productIds,
            $categoryIds,
            $vendorIds
        ) {
            $offer = Offer::create($validated);

            $offer->products()->sync($productIds);
            $offer->categories()->sync($categoryIds);
            $offer->vendors()->sync($vendorIds);
        });

        return redirect()
            ->route('admin.offers.index')
            ->with('success', 'Offer created successfully.');
    }

    public function edit(Offer $offer)
    {
        $offer->load(['products', 'categories', 'vendors']);

        return view('admin.offers.edit', compact('offer'));
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $this->validateOffer($request);

        $productIds = $validated['product_ids'] ?? [];
        $categoryIds = $validated['category_ids'] ?? [];
        $vendorIds = $validated['vendor_ids'] ?? [];

        unset(
            $validated['product_ids'],
            $validated['category_ids'],
            $validated['vendor_ids']
        );

        // Keep existing slug stable when the offer name changes.
        $validated['slug'] = $offer->slug;

        DB::transaction(function () use (
            $offer,
            $validated,
            $productIds,
            $categoryIds,
            $vendorIds
        ) {
            $offer->update($validated);

            $offer->products()->sync($productIds);
            $offer->categories()->sync($categoryIds);
            $offer->vendors()->sync($vendorIds);
        });

        return redirect()
            ->route('admin.offers.index')
            ->with('success', 'Offer updated successfully.');
    }

    public function toggleStatus(Offer $offer)
    {
        if ($offer->status === 'expired') {
            return back()->with(
                'error',
                'Expired offers cannot be activated. Update the expiry date first.'
            );
        }

        $offer->update([
            'status' => $offer->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        return back()->with('success', 'Offer status updated successfully.');
    }

    public function destroy(Offer $offer)
    {
        DB::transaction(function () use ($offer) {
            $offer->products()->detach();
            $offer->categories()->detach();
            $offer->vendors()->detach();
            $offer->delete();
        });

        return redirect()
            ->route('admin.offers.index')
            ->with('success', 'Offer deleted successfully.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Offer::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function validateOffer(Request $request): array
    {
       $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'offer_type' => [
                'required',
                Rule::in(['percentage', 'fixed', 'free_shipping', 'bogo']),
            ],

            'discount_value' => 'nullable|required_if:offer_type,percentage,fixed|numeric|min:0',

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

            'buy_quantity' => [
    'exclude_unless:offer_type,bogo',
    'required',
    'integer',
    'in:1',
],

'get_quantity' => [
    'exclude_unless:offer_type,bogo',
    'required',
    'integer',
    'in:1',
],

            'usage_limit' => ['nullable', 'integer', 'min:1'],

            'is_stackable' => ['nullable', 'boolean'],

            'priority' => ['required', 'integer', 'min:0', 'max:10000'],

            'starts_at' => [
    'nullable',
    'date',
],

'expires_at' => [
    'nullable',
    'date',
    'after:starts_at',
],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

          'product_ids' => [
    'nullable',
    'array',
    'prohibited_if:offer_type,bogo',
],

'product_ids.*' => [
    'integer',
    'distinct',
    'exists:products,id',
],

'category_ids' => [
    'nullable',
    'array',
    'prohibited_if:offer_type,bogo',
],

'category_ids.*' => [
    'integer',
    'distinct',
    'exists:categories,id',
],

'vendor_ids' => [
    'nullable',
    'array',
    'prohibited_if:offer_type,bogo',
],

'vendor_ids.*' => [
    'integer',
    'distinct',
    'exists:vendors,id',
],
        ]);

foreach (['starts_at', 'expires_at'] as $field) {
    if (!empty($validated[$field])) {
        $validated[$field] = \Carbon\Carbon::parse(
            $validated[$field]
        )->format('Y-m-d H:i:s');
    }
}

return $validated;
    }
}
