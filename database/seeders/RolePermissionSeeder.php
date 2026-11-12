<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | Define Permissions
            |--------------------------------------------------------------------------
            */

            $permissions = [

                // Admin permissions
                ['name' => 'Manage Users', 'slug' => 'manage_users', 'module' => 'users'],
                ['name' => 'Manage Vendors', 'slug' => 'manage_vendors', 'module' => 'vendors'],
                ['name' => 'Approve Vendors', 'slug' => 'approve_vendors', 'module' => 'vendors'],
                ['name' => 'Manage Products', 'slug' => 'manage_products', 'module' => 'products'],
                ['name' => 'Approve Products', 'slug' => 'approve_products', 'module' => 'products'],
                ['name' => 'Manage Categories', 'slug' => 'manage_categories', 'module' => 'categories'],
                ['name' => 'Manage Brands', 'slug' => 'manage_brands', 'module' => 'brands'],
                ['name' => 'Manage Orders', 'slug' => 'manage_orders', 'module' => 'orders'],
                ['name' => 'Manage Payments', 'slug' => 'manage_payments', 'module' => 'payments'],
                ['name' => 'Manage Coupons', 'slug' => 'manage_coupons', 'module' => 'coupons'],
                ['name' => 'Manage Offers', 'slug' => 'manage_offers', 'module' => 'offers'],
                ['name' => 'Manage Commissions', 'slug' => 'manage_commissions', 'module' => 'commissions'],
                ['name' => 'Manage Payouts', 'slug' => 'manage_payouts', 'module' => 'payouts'],
                ['name' => 'View Reports', 'slug' => 'view_reports', 'module' => 'reports'],
                ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'module' => 'settings'],
                ['name' => 'Manage Reviews', 'slug' => 'manage_reviews', 'module' => 'reviews'],
                ['name' => 'View Audit Logs', 'slug' => 'view_audit_logs', 'module' => 'audit_logs'],

                // Vendor permissions
                ['name' => 'Manage Own Shop', 'slug' => 'manage_own_shop', 'module' => 'shop'],
                ['name' => 'Manage Own Products', 'slug' => 'manage_own_products', 'module' => 'products'],
                ['name' => 'Manage Own Inventory', 'slug' => 'manage_own_inventory', 'module' => 'inventory'],
                ['name' => 'View Own Orders', 'slug' => 'view_own_orders', 'module' => 'orders'],
                ['name' => 'Manage Own Orders', 'slug' => 'manage_own_orders', 'module' => 'orders'],
                ['name' => 'View Own Earnings', 'slug' => 'view_own_earnings', 'module' => 'earnings'],
                ['name' => 'Request Payouts', 'slug' => 'request_payouts', 'module' => 'payouts'],
                ['name' => 'Request Coupons', 'slug' => 'request_coupons', 'module' => 'coupons'],
                ['name' => 'Request Offers', 'slug' => 'request_offers', 'module' => 'offers'],
                ['name' => 'View Own Reports', 'slug' => 'view_own_reports', 'module' => 'reports'],

                // Customer permissions
                ['name' => 'Manage Own Profile', 'slug' => 'manage_profile', 'module' => 'profile'],
                ['name' => 'Manage Own Addresses', 'slug' => 'manage_addresses', 'module' => 'addresses'],
                ['name' => 'Manage Own Cart', 'slug' => 'manage_cart', 'module' => 'cart'],
                ['name' => 'Manage Own Wishlist', 'slug' => 'manage_wishlist', 'module' => 'wishlist'],
                ['name' => 'Place Orders', 'slug' => 'place_orders', 'module' => 'orders'],
                ['name' => 'View Own Orders', 'slug' => 'view_customer_orders', 'module' => 'orders'],
                ['name' => 'Write Reviews', 'slug' => 'write_reviews', 'module' => 'reviews'],
                ['name' => 'Use Coupons', 'slug' => 'use_coupons', 'module' => 'coupons'],

            ];

            /*
            |--------------------------------------------------------------------------
            | Create Or Update Permissions
            |--------------------------------------------------------------------------
            */

            $permissionIds = [];

            foreach ($permissions as $permissionData) {

                $permission = Permission::updateOrCreate(
                    ['slug' => $permissionData['slug']],
                    $permissionData
                );

                $permissionIds[$permissionData['slug']] = $permission->id;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Default Roles
            |--------------------------------------------------------------------------
            */

            $admin = Role::updateOrCreate(
                ['slug' => 'admin'],
                [
                    'name' => 'Administrator',
                    'description' => 'Complete marketplace administration access.',
                    'is_system' => true,
                    'status' => true,
                ]
            );

            $vendor = Role::updateOrCreate(
                ['slug' => 'vendor'],
                [
                    'name' => 'Vendor',
                    'description' => 'Marketplace seller with vendor-specific access.',
                    'is_system' => true,
                    'status' => true,
                ]
            );

            $customer = Role::updateOrCreate(
                ['slug' => 'customer'],
                [
                    'name' => 'Customer',
                    'description' => 'Marketplace customer account.',
                    'is_system' => true,
                    'status' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Assign Permissions To Roles
            |--------------------------------------------------------------------------
            */

            // Admin gets every permission defined above.
            $admin->permissions()->syncWithoutDetaching(
                array_values($permissionIds)
            );

            // Vendor permissions.
            $vendorPermissions = [
                'manage_own_shop',
                'manage_own_products',
                'manage_own_inventory',
                'view_own_orders',
                'manage_own_orders',
                'view_own_earnings',
                'request_payouts',
                'request_coupons',
                'request_offers',
                'view_own_reports',
            ];

            $vendor->permissions()->syncWithoutDetaching(
                array_intersect_key(
                    $permissionIds,
                    array_flip($vendorPermissions)
                )
            );

            // Customer permissions.
            $customerPermissions = [
                'manage_profile',
                'manage_addresses',
                'manage_cart',
                'manage_wishlist',
                'place_orders',
                'view_customer_orders',
                'write_reviews',
                'use_coupons',
            ];

            $customer->permissions()->syncWithoutDetaching(
                array_intersect_key(
                    $permissionIds,
                    array_flip($customerPermissions)
                )
            );

        });

        $this->command->info('ESS MARKETPLACE roles and permissions created successfully.');
    }
}
