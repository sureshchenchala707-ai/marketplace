<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\CustomerRegistrationController;
use App\Http\Controllers\Auth\VendorRegistrationController;

use App\Http\Controllers\Admin\VendorApprovalController;
use App\Http\Controllers\Admin\AdminDashboardController;

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Customer\RazorpayPaymentController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OfferManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Vendor\ShopProfileController;
use App\Http\Controllers\Admin\PaymentManagementController;


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', [StorefrontController::class, 'index'])
    ->name('home');

Route::get('/products/{product:slug}', [StorefrontController::class, 'show'])
    ->name('products.show');

/*
|--------------------------------------------------------------------------
| Customer Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [CustomerAuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [CustomerAuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');

Route::get('/register', [CustomerRegistrationController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [CustomerRegistrationController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.submit');


/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.submit');

});


/*
|--------------------------------------------------------------------------
| Vendor Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/login', [VendorAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [VendorAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.submit');

    Route::get('/register', [VendorRegistrationController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [VendorRegistrationController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.submit');

});


/*
|--------------------------------------------------------------------------
| Protected Admin Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/vendors', [VendorApprovalController::class, 'index'])
            ->name('vendors.index');

        Route::get('/vendors/{vendor}', [VendorApprovalController::class, 'show'])
            ->name('vendors.show');

        Route::post('/vendors/{vendor}/approve', [VendorApprovalController::class, 'approve'])
            ->name('vendors.approve');

        Route::post('/vendors/{vendor}/reject', [VendorApprovalController::class, 'reject'])
            ->name('vendors.reject');

            // Suspend an approved vendor
        Route::post('/vendors/{vendor}/suspend', [VendorApprovalController::class, 'suspend'])
            ->name('vendors.suspend');

        // Reactivate a suspended vendor
        Route::post('/vendors/{vendor}/reactivate', [VendorApprovalController::class, 'reactivate'])
            ->name('vendors.reactivate');

            // Admin User Management
        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus'])
            ->name('users.status');

        // Admin Product Management
        Route::get('/products', [\App\Http\Controllers\Admin\ProductManagementController::class, 'index'])
            ->name('products.index');

        Route::get('/products/{product}', [\App\Http\Controllers\Admin\ProductManagementController::class, 'show'])
            ->name('products.show');

        // Product Approval
        Route::post('/products/{product}/approve', [
            \App\Http\Controllers\Admin\ProductManagementController::class,
            'approve'
        ])->name('products.approve');

        Route::post('/products/{product}/reject', [
            \App\Http\Controllers\Admin\ProductManagementController::class,
            'reject'
        ])->name('products.reject');

        Route::post('/reviews/{review}/approve',
            [\App\Http\Controllers\Admin\ProductManagementController::class, 'approveReview']
        )->name('reviews.approve');

        Route::post('/reviews/{review}/reject',
            [\App\Http\Controllers\Admin\ProductManagementController::class, 'rejectReview']
        )->name('reviews.reject');

        // Admin Order Management
        Route::get('/orders', [
            \App\Http\Controllers\Admin\OrderManagementController::class,
            'index'
        ])->name('orders.index');

        Route::get('/orders/{order}', [
            \App\Http\Controllers\Admin\OrderManagementController::class,
            'show'
        ])->name('orders.show');

        Route::patch('/orders/{order}/status', [
            \App\Http\Controllers\Admin\OrderManagementController::class,
            'updateStatus'
        ])->name('orders.status');

        Route::patch(
            '/orders/{order}/cod-payment-received',[
                \App\Http\Controllers\Admin\OrderManagementController::class, 'markCodPaymentReceived']
        )->name('orders.cod-payment-received');


        // Admin Coupon Management
        Route::get('/coupons', [CouponController::class, 'index'])
            ->name('coupons.index');

        Route::get('/coupons/create', [CouponController::class, 'create'])
            ->name('coupons.create');

        Route::post('/coupons', [CouponController::class, 'store'])
            ->name('coupons.store');

        Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])
            ->name('coupons.edit');

        Route::put('/coupons/{coupon}', [CouponController::class, 'update'])
            ->name('coupons.update');

        Route::patch('/coupons/{coupon}/status', [CouponController::class, 'toggleStatus'])
            ->name('coupons.status');

        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])
            ->name('coupons.destroy');

        Route::get('/offers', [OfferManagementController::class, 'index'])
    ->name('offers.index');

        Route::get('/offers/create', [OfferManagementController::class, 'create'])
            ->name('offers.create');

        Route::post('/offers', [OfferManagementController::class, 'store'])
            ->name('offers.store');

        Route::get('/offers/{offer}/edit', [OfferManagementController::class, 'edit'])
            ->name('offers.edit');

        Route::put('/offers/{offer}', [OfferManagementController::class, 'update'])
            ->name('offers.update');

        Route::patch('/offers/{offer}/status', [OfferManagementController::class, 'toggleStatus'])
            ->name('offers.status');

        Route::delete('/offers/{offer}', [OfferManagementController::class, 'destroy'])
            ->name('offers.destroy');

        Route::get('/payments', [PaymentManagementController::class, 'index'])
    ->name('payments.index');

    });


/*
|--------------------------------------------------------------------------
| Protected Vendor Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:vendor', 'vendor.approved'])
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('vendor.dashboard');
        })->name('dashboard');

        Route::post('/logout', [VendorAuthController::class, 'logout'])
            ->name('logout');

        Route::delete('/products/{product}/variants/{variant}',
            [\App\Http\Controllers\Vendor\ProductController::class, 'destroyVariant']
        )->name('products.variants.destroy');

        Route::resource('/products', \App\Http\Controllers\Vendor\ProductController::class)
        ->names('products');

        // Vendor Order Management
        Route::get('/orders', [
            \App\Http\Controllers\Vendor\OrderController::class,
            'index'
        ])->name('orders.index');

        Route::get('/orders/{order}', [
            \App\Http\Controllers\Vendor\OrderController::class,
            'show'
        ])->name('orders.show');

        Route::patch('/orders/items/{orderItem}/status', [
            \App\Http\Controllers\Vendor\OrderController::class,
            'updateItemStatus'
        ])->name('orders.items.status');

        Route::get('/profile', [ShopProfileController::class, 'edit'])
    ->name('profile.edit');

Route::put('/profile', [ShopProfileController::class, 'update'])
    ->name('profile.update');

    });


/*
|--------------------------------------------------------------------------
| Protected Customer Account
|--------------------------------------------------------------------------
*/
Route::post('/razorpay/webhook', [
    RazorpayPaymentController::class,
    'webhook',
])->name('razorpay.webhook');

Route::middleware(['auth', 'active', 'role:customer'])

    ->prefix('account')
    ->name('account.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('customer.dashboard');
        })->name('dashboard');

        // Shopping Cart
        Route::get('/cart', [
            \App\Http\Controllers\Customer\CartController::class,
            'index'
        ])->name('cart.index');

        Route::post('/cart', [
            \App\Http\Controllers\Customer\CartController::class,
            'store'
        ])->middleware('throttle:30,1')->name('cart.store');

        Route::patch('/cart/{cartItem}', [
            \App\Http\Controllers\Customer\CartController::class,
            'update'
        ])->name('cart.update');

        Route::delete('/cart/{cartItem}', [
            \App\Http\Controllers\Customer\CartController::class,
            'destroy'
        ])->name('cart.destroy');

        Route::delete('/cart', [
            \App\Http\Controllers\Customer\CartController::class,
            'clear'
        ])->name('cart.clear');

        // Wishlist
        Route::get('/wishlist', [
            \App\Http\Controllers\Customer\WishlistController::class,
            'index'
        ])->name('wishlist.index');

        Route::post('/wishlist', [
            \App\Http\Controllers\Customer\WishlistController::class,
            'store'
        ])->middleware('throttle:30,1')->name('wishlist.store');

        Route::delete('/wishlist/{wishlist}', [
            \App\Http\Controllers\Customer\WishlistController::class,
            'destroy'
        ])->name('wishlist.destroy');

        Route::get('/profile', [
            \App\Http\Controllers\Customer\ProfileController::class,
            'edit'
        ])->name('profile.edit');

        Route::put('/profile', [
            \App\Http\Controllers\Customer\ProfileController::class,
            'update'
        ])->name('profile.update');

        // Customer Address Management
        Route::resource('/addresses',
            \App\Http\Controllers\Customer\AddressController::class
        )->except(['show'])->names('addresses');

        Route::post('/addresses/{address}/default',
            [\App\Http\Controllers\Customer\AddressController::class, 'setDefault']
        )->name('addresses.default');

        // Checkout
        Route::get('/checkout', [
            \App\Http\Controllers\Customer\CheckoutController::class,
            'index'
        ])->name('checkout.index');

        Route::post('/checkout/place-order', [
            \App\Http\Controllers\Customer\CheckoutController::class,
            'placeOrder'
        ])->middleware('throttle:10,1')->name('checkout.place-order');

        // Customer Orders
        Route::get('/orders', [
            \App\Http\Controllers\Customer\OrderController::class,
            'index'
        ])->name('orders.index');

        Route::get('/orders/{order}', [
            \App\Http\Controllers\Customer\OrderController::class,
            'show'
        ])->name('orders.show');

        Route::get('/orders/{order}/razorpay', [RazorpayPaymentController::class, 'initiate'])
            ->name('orders.razorpay.initiate');

        Route::post('/orders/{order}/razorpay/verify', [RazorpayPaymentController::class, 'verify'])
            ->name('orders.razorpay.verify');

        Route::post(
                '/orders/{order}/razorpay/failure',
                [RazorpayPaymentController::class, 'failure']
            )->name('orders.razorpay.failure');

        Route::post('/checkout/coupon/apply', [
            \App\Http\Controllers\Customer\CheckoutController::class,
            'applyCoupon'
        ])->name('checkout.coupon.apply');

        Route::delete('/checkout/coupon/remove', [
            \App\Http\Controllers\Customer\CheckoutController::class,
            'removeCoupon'
        ])->name('checkout.coupon.remove');

        Route::post('/logout', [CustomerAuthController::class, 'logout'])
            ->name('logout');

    });

    Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home')
        ->with('success', 'You have been logged out successfully.');
})->middleware('auth')->name('logout');


use App\Http\Controllers\NotificationController;

Route::middleware(['auth', 'active'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::get('/recent', [NotificationController::class, 'recent'])
            ->name('recent');

        Route::patch('/{id}/read', [NotificationController::class, 'markRead'])
            ->name('read');

        Route::patch('/read-all', [NotificationController::class, 'markAllRead'])
            ->name('readAll');

        Route::delete('/{id}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });