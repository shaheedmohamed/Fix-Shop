<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('buyer_seller_conversations')) {
            Schema::create('buyer_seller_conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
                $table->unique(['buyer_id','seller_id','product_id']);
            });
        }

        if (!Schema::hasTable('buyer_seller_messages')) {
            Schema::create('buyer_seller_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('buyer_seller_conversations')->onDelete('cascade');
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->text('body');
                $table->timestamps();
                $table->index(['conversation_id','created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
