<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'children' => [
                    'Mobiles & Accessories',
                    'Laptops & Computers',
                    'Televisions',
                    'Audio & Headphones',
                    'Cameras',
                    'Smart Watches',
                ],
            ],
            [
                'name' => 'Fashion',
                'children' => [
                    'Men Clothing',
                    'Women Clothing',
                    'Kids Clothing',
                    'Footwear',
                    'Watches',
                    'Bags & Accessories',
                ],
            ],
            [
                'name' => 'Home & Kitchen',
                'children' => [
                    'Kitchen Appliances',
                    'Home Decor',
                    'Furniture',
                    'Home Furnishing',
                    'Cookware',
                    'Lighting',
                ],
            ],
            [
                'name' => 'Beauty & Personal Care',
                'children' => [
                    'Skincare',
                    'Haircare',
                    'Makeup',
                    'Fragrances',
                    'Personal Hygiene',
                ],
            ],
            [
                'name' => 'Sports & Fitness',
                'children' => [
                    'Fitness Equipment',
                    'Sportswear',
                    'Outdoor Recreation',
                    'Cycling',
                    'Yoga Accessories',
                ],
            ],
            [
                'name' => 'Books & Stationery',
                'children' => [
                    'Educational Books',
                    'Fiction & Literature',
                    'Office Supplies',
                    'School Stationery',
                ],
            ],
            [
                'name' => 'Toys & Baby Products',
                'children' => [
                    'Baby Care',
                    'Baby Clothing',
                    'Educational Toys',
                    'Indoor Games',
                    'Outdoor Toys',
                ],
            ],
            [
                'name' => 'Grocery & Food',
                'children' => [
                    'Packaged Food',
                    'Snacks & Beverages',
                    'Organic Products',
                    'Daily Essentials',
                ],
            ],
            [
                'name' => 'Automotive',
                'children' => [
                    'Car Accessories',
                    'Bike Accessories',
                    'Car Care',
                    'Automotive Tools',
                ],
            ],
            [
                'name' => 'Health & Wellness',
                'children' => [
                    'Personal Wellness',
                    'Fitness Accessories',
                    'Health Devices',
                ],
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'parent_id' => null,
                    'name' => $categoryData['name'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );

            foreach ($categoryData['children'] as $childIndex => $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'is_active' => true,
                        'sort_order' => $childIndex + 1,
                    ]
                );
            }
        }
    }
}
