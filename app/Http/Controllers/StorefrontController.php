<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    /**
     * Customer storefront homepage and product listing.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with([
                'vendor',
                'category',
                'images',
                'variants',
                'specifications',
            ])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereHas('vendor', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereHas('category', function ($q) {
                $q->where('is_active', true);
            });

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

       if ($request->filled('category')) {
            $category = Category::with('children')->find($request->category);

            if ($category) {
                $categoryIds = collect([$category->id])
                    ->merge($category->children->pluck('id'));

                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Price filters
        if ($request->filled('min_price')) {
            $query->whereRaw(
                'COALESCE(sale_price, regular_price) >= ?',
                [(float) $request->min_price]
            );
        }

        if ($request->filled('max_price')) {
            $query->whereRaw(
                'COALESCE(sale_price, regular_price) <= ?',
                [(float) $request->max_price]
            );
        }

        // Featured products
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, regular_price) ASC');
                break;

            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, regular_price) DESC');
                break;

            case 'name':
                $query->orderBy('name');
                break;

            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::with(['images', 'vendor'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->whereHas('vendor', function ($q) {
                $q->where('status', 'approved');
            })
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.index', compact(
            'products',
            'categories',
            'featuredProducts'
        ));
    }

    /**
     * Display a single approved product.
     */
    public function show(Product $product)
    {
        abort_unless(
            $product->status === 'approved'
            && $product->is_active
            && $product->vendor?->status === 'approved'
            && $product->category?->is_active,
            404
        );

        $product->load([
            'vendor',
            'category',
            'images',
            'variants' => function ($query) {
                $query->where('is_active', true);
            },
            'specifications' => function ($query) {
                $query->orderBy('sort_order');
            },
            'reviews' => function ($query) {
                $query->where('status', 'approved')
                    ->with('user')
                    ->latest();
            },
        ]);

        $relatedProducts = Product::with(['images', 'vendor', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereHas('vendor', function ($q) {
                $q->where('status', 'approved');
            })
            ->latest()
            ->take(4)
            ->get();

        $averageRating = $product->reviews->avg('rating');
        $reviewCount = $product->reviews->count();

        return view('storefront.show', compact(
            'product',
            'relatedProducts',
            'averageRating',
            'reviewCount'
        ));
    }
}