<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subject')->nullable();
            $table->string('item_type')->nullable(); // e.g., lesson, concept
            $table->string('item_id')->nullable();
            $table->unsignedInteger('repetitions')->default(0);
            $table->float('ease')->default(2.5); // 1.3 - 2.7 (SM-2 like)
            $table->unsignedInteger('interval')->default(0); // days
            $table->timestamp('due_at')->nullable();
            $table->timestamps();

            $table->index(['user_id','due_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
