<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = auth()->user()->wishlistItems()
            ->with([
                'product.vendor',
                'product.category',
                'product.images',
            ])
            ->latest()
            ->paginate(12);

        return view('customer.wishlist.index', compact(
            'wishlistItems'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::with('vendor', 'category')
            ->findOrFail($validated['product_id']);

        abort_unless(
            $product->status === 'approved'
            && $product->is_active
            && $product->vendor?->status === 'approved'
            && $product->category?->is_active,
            422,
            'This product is currently unavailable.'
        );

        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Product added to your wishlist.');
    }

    public function destroy(Wishlist $wishlist)
    {
        abort_unless(
            $wishlist->user_id === auth()->id(),
            404
        );

        $wishlist->delete();

        return back()->with('success', 'Product removed from wishlist.');
    }
}
