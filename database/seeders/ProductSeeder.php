<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Prefer assigning products to providers (sellers)
        $providerIds = User::query()->where('role','provider')->pluck('id')->all();
        $fallbackUserId = User::query()->orderBy('id')->value('id') ?? 1;
        $categories = Category::with('subcategories:id,category_id')->get();
        if ($categories->isEmpty()) return;

        $brands = ['AmazonBasics','Samsung','Apple','Xiaomi','Anker','Sony','LG','Tornado','Fresh','Lenovo'];
        $colors = ['Black','White','Blue','Red','Green','Yellow','Gray'];
        $units  = ['pc','box','set'];

        for ($i=1; $i<=500; $i++) {
            $cat = $categories->random();
            $sub = $cat->subcategories->isNotEmpty() ? $cat->subcategories->random() : null;

            $price = rand(100, 12000);
            $hasDiscount = rand(0,1) === 1;
            $discount = $hasDiscount ? max(50, $price - rand(50, 400)) : null;

            $sku = 'SKU-'.strtoupper(Str::random(12));
            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'user_id' => $providerIds ? $providerIds[array_rand($providerIds)] : $fallbackUserId,
                    'name' => self::fakeName($cat->name),
                    'description' => 'Auto seeded product for demo and testing purposes.',
                    'price' => $price,
                    'price_discount' => $discount,
                    'image_path' => "https://picsum.photos/seed/prod{$i}-".Str::random(6)."/600/400",
                    'category_id' => $cat->id,
                    'subcategory_id' => $sub?->id,
                    'brand' => $brands[array_rand($brands)],
                    'color' => $colors[array_rand($colors)],
                    'unit' => $units[array_rand($units)],
                    'size' => null,
                    'stock_qty' => rand(0, 200),
                    'min_order_qty' => 1,
                    'seo_title' => null,
                    'seo_description' => null,
                    'seo_keywords' => null,
                ]
            );
        }
    }

    protected static function fakeName(string $categoryName): string
    {
        $samples = [
            'Automobile' => ['Car Vacuum Cleaner','Motorcycle Helmet','Car Phone Holder'],
            'Beauty & Health' => ['Skin Care Serum','Hair Dryer','Makeup Brush Set'],
            'Baby & Maternity' => ['Baby Stroller','Feeding Bottle','Baby Monitor'],
            'Jewelry & Accessories' => ['Gold Plated Necklace','Leather Bracelet','Gemstone Ring'],
            'Industry & Science' => ['Digital Caliper','Lab Beaker Set','Safety Gloves'],
            'Electronics' => ['Wireless Earbuds','Smartwatch Series','Bluetooth Speaker'],
            'Home & Kitchen' => ['Air Fryer','Ceramic Dinner Set','Scented Candles'],
            'Office & School' => ['Ergonomic Office Chair','A4 Paper Ream','All-in-One Printer'],
            'Men Large Sizes' => ['Men Oversized T-Shirt','Men Casual Pants','Running Shoes'],
        ];
        $list = $samples[$categoryName] ?? ['General Product','Premium Item','Top Seller'];
        return $list[array_rand($list)];
    }
}
