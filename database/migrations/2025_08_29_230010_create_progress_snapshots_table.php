<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('progress_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->date('date');
            $table->unsignedTinyInteger('mastery_percent')->default(0); // 0-100
            $table->decimal('hours', 5, 2)->default(0); // total hours on date
            $table->timestamps();

            $table->unique(['child_id','subject_id','date']);
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_snapshots');
    }
};
