<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Automobile' => [ 'Car Care', 'Tools & Equipment', 'Motorcycle Accessories' ],
            'Beauty & Health' => [ 'Makeup', 'Skincare', 'Hair Care' ],
            'Baby & Maternity' => [ 'Diapers', 'Feeding', 'Strollers' ],
            'Jewelry & Accessories' => [ 'Necklaces', 'Bracelets', 'Rings' ],
            'Industry & Science' => [ 'Measuring Tools', 'Lab Supplies', 'Industrial Electrical' ],
            'Electronics' => [ 'Mobiles', 'Smartwatches', 'Headphones & Earbuds' ],
            'Home & Kitchen' => [ 'Small Appliances', 'Furniture', 'Decor' ],
            'Office & School' => [ 'Stationery', 'Office Furniture', 'Printers' ],
            'Men Large Sizes' => [ 'Tops', 'Bottoms', 'Shoes' ],
        ];

        foreach ($data as $cat => $subs) {
            $c = Category::firstOrCreate(
                ['name' => $cat],
                ['slug' => str()->slug($cat)]
            );
            foreach ($subs as $s) {
                SubCategory::firstOrCreate(
                    ['name' => $s, 'category_id' => $c->id],
                    ['slug' => str()->slug($s)]
                );
            }
        }
    }
}
