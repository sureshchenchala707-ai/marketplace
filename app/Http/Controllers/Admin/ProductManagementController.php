<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductReview;

class ProductManagementController extends Controller
{
    /**
     * Display all products with search and filters.
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'vendor.user',
            'category',
            'images',
        ]);

        // Search products
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where(
                            'shop_name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        // Filter by product status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Sort products
        $query->latest();

        $products = $query->paginate(10)->withQueryString();

        // Dashboard counts
        $counts = [
            'total' => Product::count(),
            'pending' => Product::where('status', 'pending')->count(),
            'approved' => Product::where('status', 'approved')->count(),
            'rejected' => Product::where('status', 'rejected')->count(),
            'draft' => Product::where('status', 'draft')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
        ];

        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $vendors = \App\Models\Vendor::where('status', 'approved')
            ->orderBy('shop_name')
            ->get(['id', 'shop_name']);

        return view('admin.products.index', compact(
            'products',
            'counts',
            'categories',
            'vendors'
        ));
    }

    /**
     * Display a single product.
     */
    public function show(Product $product)
    {
        $product->load([
            'vendor.user',
            'category',
            'images',
            'variants',
            'specifications',
            'reviews.user',
        ]);

        return view('admin.products.show', compact('product'));
    }

    /**
 * Approve a product.
 */
public function approve(Product $product)
{
    DB::transaction(function () use ($product) {
        $product = Product::whereKey($product->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($product->status !== 'pending') {
            abort(422, 'Only pending products can be approved.');
        }

        $product->update([
            'status' => 'approved',
            'admin_remarks' => null,
        ]);
    });

    return redirect()
        ->route('admin.products.show', $product)
        ->with('success', 'Product approved successfully.');
}

/**
 * Reject a product.
 */
public function reject(Request $request, Product $product)
{
    $validated = $request->validate([
        'admin_remarks' => ['required', 'string', 'max:1000'],
    ]);

    DB::transaction(function () use ($product, $validated) {
        $product = Product::whereKey($product->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($product->status !== 'pending') {
            abort(422, 'Only pending products can be rejected.');
        }

        $product->update([
            'status' => 'rejected',
            'admin_remarks' => $validated['admin_remarks'],
        ]);
    });

    return redirect()
        ->route('admin.products.show', $product)
        ->with('success', 'Product rejected successfully.');
}


/**
 * Approve a customer product review.
 */
public function approveReview(ProductReview $review)
{
    DB::transaction(function () use ($review) {

        $lockedReview = ProductReview::whereKey($review->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedReview->status !== 'pending') {
            abort(422, 'Only pending reviews can be approved.');
        }

        $lockedReview->update([
            'status' => 'approved',
        ]);
    });

    return back()->with('success', 'Review approved successfully.');
}

/**
 * Reject a customer product review.
 */
public function rejectReview(ProductReview $review)
{
    DB::transaction(function () use ($review) {

        $lockedReview = ProductReview::whereKey($review->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedReview->status !== 'pending') {
            abort(422, 'Only pending reviews can be rejected.');
        }

        $lockedReview->update([
            'status' => 'rejected',
        ]);
    });

    return back()->with('success', 'Review rejected successfully.');
}
}