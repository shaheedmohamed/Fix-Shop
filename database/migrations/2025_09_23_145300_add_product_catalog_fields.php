<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                      ->nullable()
                      ->constrained('categories')
                      ->nullOnDelete()
                      ->after('user_id');
            }

            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->foreignId('subcategory_id')
                      ->nullable()
                      ->constrained('sub_categories')
                      ->nullOnDelete()
                      ->after('category_id');
            }

            if (!Schema::hasColumn('products', 'brand')) $table->string('brand')->nullable()->after('subcategory_id');
            if (!Schema::hasColumn('products', 'color')) $table->string('color')->nullable()->after('brand');
            if (!Schema::hasColumn('products', 'unit')) $table->string('unit')->nullable()->after('color');
            if (!Schema::hasColumn('products', 'size')) $table->string('size')->nullable()->after('unit');
            if (!Schema::hasColumn('products', 'sku')) $table->string('sku')->nullable()->unique()->after('size');
            if (!Schema::hasColumn('products', 'stock_qty')) $table->integer('stock_qty')->default(0)->after('sku');
            if (!Schema::hasColumn('products', 'min_order_qty')) $table->integer('min_order_qty')->default(1)->after('stock_qty');
            if (!Schema::hasColumn('products', 'price_discount')) $table->decimal('price_discount', 10, 2)->nullable()->after('price');
            if (!Schema::hasColumn('products', 'seo_title')) $table->string('seo_title')->nullable()->after('image_path');
            if (!Schema::hasColumn('products', 'seo_description')) $table->text('seo_description')->nullable()->after('seo_title');
            if (!Schema::hasColumn('products', 'seo_keywords')) $table->text('seo_keywords')->nullable()->after('seo_description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // لازم الأول نشيل الـ foreign keys
            if (Schema::hasColumn('products', 'subcategory_id')) {
                $table->dropForeign(['subcategory_id']);
                $table->dropColumn('subcategory_id');
            }

            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }

            foreach (['brand','color','unit','size','sku','stock_qty','min_order_qty','price_discount','seo_title','seo_description','seo_keywords'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
