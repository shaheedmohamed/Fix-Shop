<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->unsignedBigInteger('child_user_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('child_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
