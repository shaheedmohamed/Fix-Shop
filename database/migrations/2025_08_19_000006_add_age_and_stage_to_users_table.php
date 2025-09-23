<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'education_stage')) {
                $table->string('education_stage')->nullable()->after('age');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'education_stage')) {
                $table->dropColumn('education_stage');
            }
            if (Schema::hasColumn('users', 'age')) {
                $table->dropColumn('age');
            }
        });
    }
};
