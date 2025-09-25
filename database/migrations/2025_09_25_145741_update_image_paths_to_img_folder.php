<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update products table
        DB::table('products')
            ->where('image_path', 'like', 'uploads/products/%')
            ->update([
                'image_path' => DB::raw("REPLACE(image_path, 'uploads/products/', 'img/')")
            ]);

        DB::table('products')
            ->where('image_path', 'like', 'products/%')
            ->update([
                'image_path' => DB::raw("REPLACE(image_path, 'products/', 'img/')")
            ]);

        // Update posts table if exists
        if (Schema::hasTable('posts')) {
            DB::table('posts')
                ->where('image_path', 'like', 'uploads/posts/%')
                ->update([
                    'image_path' => DB::raw("REPLACE(image_path, 'uploads/posts/', 'img/')")
                ]);
        }

        // Update users table for logos
        DB::table('users')
            ->where('logo_path', 'like', 'uploads/logos/%')
            ->update([
                'logo_path' => DB::raw("REPLACE(logo_path, 'uploads/logos/', 'img/')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('img_folder', function (Blueprint $table) {
            //
        });
    }
};
