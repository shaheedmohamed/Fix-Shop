<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('stage')->nullable(); // Primary, Preparatory, Secondary
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable(); // stored path on public disk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
