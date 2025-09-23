<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_name')->nullable();
            $table->enum('type', ['lesson','game','quiz'])->index();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedTinyInteger('score')->nullable(); // 0-100
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->index(['child_id','subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_activities');
    }
};
