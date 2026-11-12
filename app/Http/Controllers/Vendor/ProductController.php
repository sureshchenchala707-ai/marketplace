<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    /**
     * Display vendor's products.
     */
    public function index(Request $request)
    {
        $vendor = auth()->user()->vendor;

        $query = Product::where('vendor_id', $vendor->id)
            ->with(['category', 'images']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'total' => Product::where('vendor_id', $vendor->id)->count(),
            'pending' => Product::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'approved' => Product::where('vendor_id', $vendor->id)->where('status', 'approved')->count(),
            'rejected' => Product::where('vendor_id', $vendor->id)->where('status', 'rejected')->count(),
            'draft' => Product::where('vendor_id', $vendor->id)->where('status', 'draft')->count(),
        ];

        return view('vendor.products.index', compact('products', 'counts'));
    }

    /**
     * Show product creation form.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Store a new product.
     */

/**
 * Store a new product.
 */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'category_id' => [
                    'required',
                    'integer',
                    'exists:categories,id,is_active,1',
                ],
                'brand' => ['nullable', 'string', 'max:150'],
                'short_description' => ['nullable', 'string', 'max:1000'],
                'description' => ['nullable', 'string'],
                'regular_price' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
                'sale_price' => [
                    'nullable',
                    'numeric',
                    'min:0.01',
                    'max:9999999999.99',
                    'lt:regular_price',
                ],
                'stock_quantity' => ['required', 'integer', 'min:0'],
                'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
                'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
                'weight' => ['nullable', 'numeric', 'gt:0', 'max:9999999.999'],
                'weight_unit' => ['nullable', 'in:kg,g,lb'],
                'images' => ['nullable', 'array', 'max:5'],
                'images.*' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ], [
                'sale_price.lt' => 'Sale price must be less than regular price.',
                'images.max' => 'You can upload a maximum of 5 product images.',
                'images.*.max' => 'Each product image must not exceed 2 MB.',
            ]);

            $vendor = $request->user()->vendor;

            if (!$vendor || $vendor->status !== 'approved') {
                abort(403, 'Your vendor account is not approved.');
            }

            $uploadedPaths = [];

            try {
                $product = DB::transaction(function () use (
                    $validated,
                    $request,
                    $vendor,
                    &$uploadedPaths
                ) {
                    $sku = $validated['sku'] ?? null;

                    if (!$sku) {
                        do {
                            $sku = 'ESS-' . strtoupper(Str::random(12));
                        } while (Product::where('sku', $sku)->exists());
                    }

                    $slugBase = Str::slug($validated['name']);
                    $slug = $slugBase . '-' . Str::lower(Str::random(8));

                    $product = Product::create([
                        'vendor_id' => $vendor->id,
                        'category_id' => $validated['category_id'],
                        'name' => $validated['name'],
                        'slug' => $slug,
                        'sku' => $sku,
                        'brand' => $validated['brand'] ?? null,
                        'short_description' => $validated['short_description'] ?? null,
                        'description' => $validated['description'] ?? null,
                        'regular_price' => $validated['regular_price'],
                        'sale_price' => $validated['sale_price'] ?? null,
                        'stock_quantity' => $validated['stock_quantity'],
                        'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                        'weight' => $validated['weight'] ?? null,
                        'weight_unit' => $validated['weight_unit'] ?? null,
                        'status' => 'pending',
                        'is_featured' => false,
                        'is_active' => true,
                    ]);

                    if ($request->hasFile('images')) {
                        foreach ($request->file('images') as $index => $image) {
                            $path = $image->store('products/' . $product->id, 'public');

                            if (!$path) {
                                throw new \RuntimeException('Product image upload failed.');
                            }

                            $uploadedPaths[] = $path;

                            ProductImage::create([
                                'product_id' => $product->id,
                                'image_path' => $path,
                                'alt_text' => $product->name,
                                'is_primary' => $index === 0,
                                'sort_order' => $index,
                            ]);
                        }
                    }

                    return $product;
                });

            } catch (\Throwable $exception) {
                foreach ($uploadedPaths as $path) {
                    Storage::disk('public')->delete($path);
                }

                throw $exception;
            }

            return redirect()
                ->route('vendor.products.index')
                ->with('success', 'Product submitted successfully! It is awaiting admin approval.');
        }

    /**
     * Display a product.
     */
    public function show(Product $product)
    {
        $this->authorizeVendorProduct($product);

        $product->load(['category', 'images', 'variants', 'specifications']);

        return view('vendor.products.show', compact('product'));
    }

    /**
     * Show product edit form.
     */
    public function edit(Product $product)
    {
        $this->authorizeVendorProduct($product);

        $product->load('variants');

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('vendor.products.edit', compact('product', 'categories'));
    }
    /**
     * Update product.
     */

    public function update(Request $request, Product $product)
    {
        // Verify product ownership.
        $this->authorizeVendorProduct($product);

        // Validate product and variant data.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:categories,id,is_active,1'],
            'brand' => ['nullable', 'string', 'max:150'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],

            'regular_price' => [
                'required', 'numeric', 'min:0.01', 'max:9999999999.99'
            ],
            'sale_price' => [
                'nullable', 'numeric', 'min:0.01',
                'max:9999999999.99', 'lt:regular_price'
            ],

            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],

            'weight' => ['nullable', 'numeric', 'gt:0', 'max:9999999.999'],
            'weight_unit' => ['nullable', 'in:kg,g,lb'],

            // Variants
            'variants' => ['nullable', 'array', 'max:20'],
            'variants.*.id' => ['nullable', 'integer', 'distinct'],
            'variants.*.variant_name' => ['required', 'string', 'max:150'],
            'variants.*.sku' => ['required', 'string', 'max:100'],
            'variants.*.attributes' => ['nullable', 'string', 'max:1000'],
            'variants.*.regular_price' => [
                'required', 'numeric', 'min:0.01', 'max:9999999999.99'
            ],
            'variants.*.sale_price' => [
                'nullable', 'numeric', 'min:0.01', 'max:9999999999.99'
            ],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],

            'remove_variants' => ['nullable', 'array'],
            'remove_variants.*' => ['required', 'integer', 'distinct'],

            // Images
            'images' => ['nullable', 'array'],
            'images.*' => [
                'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'
            ],

            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['required', 'integer', 'distinct'],

            'specifications' => ['nullable', 'array', 'max:50'],
            'specifications.*.id' => ['nullable', 'integer'],
            'specifications.*.attribute_name' => ['required', 'string', 'max:150'],
            'specifications.*.attribute_value' => ['required', 'string'],

        ], [
            'sale_price.lt' => 'Sale price must be lower than regular price.',
            'images.*.max' => 'Each image must not exceed 2 MB.',
        ]);

        // Collect variant removal IDs.
        $removeVariantIds = $validated['remove_variants'] ?? [];

        // Verify submitted existing variants belong to this product.
        foreach ($validated['variants'] ?? [] as $variantData) {

            if (!empty($variantData['id'])) {

                $belongsToProduct = $product->variants()
                    ->whereKey($variantData['id'])
                    ->exists();

                if (!$belongsToProduct) {
                    return back()
                        ->withErrors(['variants' => 'Invalid variant selected.'])
                        ->withInput();
                }
            }
        }

        // Verify variant removal selections.
        $variantsToRemove = $product->variants()
            ->whereIn('id', $removeVariantIds)
            ->get();

        if ($variantsToRemove->count() !== count($removeVariantIds)) {
            return back()
                ->withErrors([
                    'remove_variants' => 'Invalid variant removal selection.'
                ])
                ->withInput();
        }

        // Validate variant SKU uniqueness and sale prices.
        // Validate variant SKU uniqueness and sale prices.
$submittedSkus = [];

$variantsBeingRemoved = array_map(
    'intval',
    $removeVariantIds
);

foreach ($validated['variants'] ?? [] as $variantData) {

    $sku = trim($variantData['sku']);
    $variantId = $variantData['id'] ?? null;

    // Skip variants selected for removal.
    if (
        $variantId &&
        in_array((int) $variantId, $variantsBeingRemoved, true)
    ) {
        continue;
    }

    // Check duplicate SKU inside submitted variants.
    $normalizedSku = strtolower($sku);

    if (in_array($normalizedSku, $submittedSkus, true)) {
        return back()
            ->withErrors([
                'variants' => "Duplicate SKU {$sku}. Each variant must have a unique SKU."
            ])
            ->withInput();
    }

        $submittedSkus[] = $normalizedSku;

        // Check duplicate SKU in database.
        $duplicateSku = ProductVariant::whereRaw(
            'LOWER(sku) = ?',
            [$normalizedSku]
        )
        ->when(
            $variantId,
            fn ($query) => $query->where('id', '!=', $variantId)
        )
        ->when(
            !empty($variantsBeingRemoved),
            fn ($query) => $query->whereNotIn('id', $variantsBeingRemoved)
        )
        ->exists();

        if ($duplicateSku) {
            return back()
                ->withErrors([
                    'variants' => "Variant SKU {$sku} is already in use."
                ])
                ->withInput();
        }

        // Sale price validation.
        if (
            !empty($variantData['sale_price']) &&
            (float) $variantData['sale_price'] >=
            (float) $variantData['regular_price']
        ) {
            return back()
                ->withErrors([
                    'variants' => 'Variant sale price must be lower than regular price.'
                ])
                ->withInput();
        }
    }

        // Verify image removal selections.
        $removeImageIds = $validated['remove_images'] ?? [];

        $imagesToRemove = ProductImage::where('product_id', $product->id)
            ->whereIn('id', $removeImageIds)
            ->get();

        if ($imagesToRemove->count() !== count($removeImageIds)) {
            return back()
                ->withErrors(['remove_images' => 'Invalid image selection.'])
                ->withInput();
        }

        // Maximum 5 images per product.
        $existingCount = $product->images()->count();
        $newImageCount = count($request->file('images', []));
        $remainingCount = $existingCount - $imagesToRemove->count();

        if (($remainingCount + $newImageCount) > 5) {
            return back()
                ->withErrors([
                    'images' => 'A product can have a maximum of 5 images.'
                ])
                ->withInput();
        }

        $uploadedPaths = [];
        $oldPaths = $imagesToRemove->pluck('image_path')->all();

        try {

            DB::transaction(function () use (
                $request,
                $product,
                $validated,
                $imagesToRemove,
                $removeVariantIds,
                &$uploadedPaths
            ) {

                // Lock product record.
                $lockedProduct = Product::whereKey($product->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->authorizeVendorProduct($lockedProduct);

                // Update product information.
                $lockedProduct->update([
                    'name' => $validated['name'],
                    'category_id' => $validated['category_id'],
                    'brand' => $validated['brand'] ?? null,
                    'short_description' => $validated['short_description'] ?? null,
                    'description' => $validated['description'] ?? null,

                    'regular_price' => $validated['regular_price'],
                    'sale_price' => $validated['sale_price'] ?? null,

                    'stock_quantity' => $validated['stock_quantity'],
                    'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,

                    'weight' => $validated['weight'] ?? null,
                    'weight_unit' => $validated['weight_unit'] ?? null,

                    // Changes require admin re-approval.
                    'status' => 'pending',
                    'admin_remarks' => null,
                ]);

                // Delete selected existing variants.
                if (!empty($removeVariantIds)) {
                    $lockedProduct->variants()
                        ->whereIn('id', $removeVariantIds)
                        ->delete();
                }

                // Create or update variants.
                foreach ($validated['variants'] ?? [] as $variantData) {

                    $variantId = $variantData['id'] ?? null;

                    // Skip variants selected for removal.
                    if (
                        $variantId &&
                        in_array((int) $variantId, array_map('intval', $removeVariantIds), true)
                    ) {
                        continue;
                    }

                    $variantValues = [
                        'variant_name' => $variantData['variant_name'],
                        'sku' => trim($variantData['sku']),
                        'attributes' => $variantData['attributes'] ?? null,
                        'regular_price' => $variantData['regular_price'],
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'],
                        'is_active' => !empty($variantData['is_active']),
                    ];

                    if ($variantId) {

                        // Update existing variant belonging to this product.
                        $lockedProduct->variants()
                            ->whereKey($variantId)
                            ->update($variantValues);

                    } else {

                        // Create new variant.
                        $lockedProduct->variants()
                            ->create($variantValues);
                    }
                }

                // Save product specifications
            $specifications = $validated['specifications'] ?? [];

            $submittedSpecificationIds = collect($specifications)
                ->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            // Delete specifications removed from the form
            $lockedProduct->specifications()
                ->when(
                    !empty($submittedSpecificationIds),
                    fn ($query) => $query->whereNotIn('id', $submittedSpecificationIds),
                    fn ($query) => $query
                )
                ->delete();

            // Update existing or create new specifications
            foreach ($specifications as $index => $specificationData) {

                $specification = $lockedProduct->specifications()
                    ->when(
                        !empty($specificationData['id']),
                        fn ($query) => $query->where('id', $specificationData['id'])
                    )
                    ->when(
                        empty($specificationData['id']),
                        fn ($query) => $query->whereRaw('1 = 0')
                    )
                    ->first();

                $data = [
                    'attribute_name' => $specificationData['attribute_name'],
                    'attribute_value' => $specificationData['attribute_value'],
                    'sort_order' => $index,
                ];

                if ($specification) {
                    $specification->update($data);
                } else {
                    $lockedProduct->specifications()->create($data);
                }
            }


                // Remove selected image records.
                foreach ($imagesToRemove as $image) {
                    $image->delete();
                }

                // Upload new images.
                $currentMaxOrder = (int) $lockedProduct->images()
                    ->max('sort_order');

                foreach ($request->file('images', []) as $index => $file) {

                    $path = $file->store(
                        'products/' . $lockedProduct->id,
                        'public'
                    );

                    if (!$path) {
                        throw new \RuntimeException('Product image upload failed.');
                    }

                    $uploadedPaths[] = $path;

                    ProductImage::create([
                        'product_id' => $lockedProduct->id,
                        'image_path' => $path,
                        'alt_text' => $lockedProduct->name,
                        'is_primary' => false,
                        'sort_order' => $currentMaxOrder + $index + 1,
                    ]);
                }

                // Assign primary image if the old primary was removed.
                $hasPrimary = $lockedProduct->images()
                    ->where('is_primary', true)
                    ->exists();

                if (!$hasPrimary) {

                    $firstImage = $lockedProduct->images()
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->first();

                    if ($firstImage) {
                        $firstImage->update([
                            'is_primary' => true
                        ]);
                    }
                }

            });

        } catch (\Throwable $exception) {

            // Delete newly uploaded files if transaction fails.
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        // Delete old image files after successful database commit.
        foreach ($oldPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('vendor.products.show', $product)
            ->with(
                'success',
                'Product updated successfully! It is awaiting admin re-approval.'
            );
    }


    /**
     * Delete product.
     */
    public function destroy(Product $product)
    {
        $this->authorizeVendorProduct($product);

        $product->delete();

        return redirect()
            ->route('vendor.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Ensure vendor can access only their own products.
     */
    private function authorizeVendorProduct(Product $product): void
    {
        abort_unless(
            $product->vendor_id === auth()->user()->vendor->id,
            403,
            'You are not authorized to access this product.'
        );
    }

    public function destroyVariant(Product $product, ProductVariant $variant)
    {
        // Ensure this product belongs to the logged-in vendor
        $this->authorizeVendorProduct($product);

        // Ensure this variant belongs to this product
        if ((int) $variant->product_id !== (int) $product->id) {
            abort(404);
        }

        DB::transaction(function () use ($variant) {
            $variant->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Variant deleted successfully.',
        ]);
    }

}
