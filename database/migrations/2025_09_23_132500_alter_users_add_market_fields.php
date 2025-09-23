<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('buyer')->after('phone');
            }
            if (!Schema::hasColumn('users', 'store_name')) {
                $table->string('store_name')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'merchant_name')) {
                $table->string('merchant_name')->nullable()->after('store_name');
            }
            if (!Schema::hasColumn('users', 'service_type')) {
                $table->text('service_type')->nullable()->after('merchant_name');
            }
            if (!Schema::hasColumn('users', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('service_type');
            }
            // Cleanup any legacy columns not needed anymore
            if (Schema::hasColumn('users', 'age')) {
                $table->dropColumn('age');
            }
            if (Schema::hasColumn('users', 'education_stage')) {
                $table->dropColumn('education_stage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'logo_path')) {
                $table->dropColumn('logo_path');
            }
            if (Schema::hasColumn('users', 'service_type')) {
                $table->dropColumn('service_type');
            }
            if (Schema::hasColumn('users', 'merchant_name')) {
                $table->dropColumn('merchant_name');
            }
            if (Schema::hasColumn('users', 'store_name')) {
                $table->dropColumn('store_name');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
