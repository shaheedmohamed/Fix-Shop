<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            // Table already exists (possibly with a different schema). Skip this migration.
            return;
        }

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed some defaults
        if (Schema::hasTable('settings')) {
            DB::table('settings')->insert([
                ['key' => 'site_slogan', 'value' => 'AI that teaches you your way', 'created_at'=>now(), 'updated_at'=>now()],
                ['key' => 'site_description', 'value' => 'Personalized lessons, an always-on AI tutor, and smart reminders.', 'created_at'=>now(), 'updated_at'=>now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

