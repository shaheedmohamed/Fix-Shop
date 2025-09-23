<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('reminders_enabled')->default(false)->after('phone');
            $table->string('reminder_time', 5)->nullable()->after('reminders_enabled'); // HH:MM
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['reminders_enabled','reminder_time']);
        });
    }
};
