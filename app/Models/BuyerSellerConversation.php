<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerSellerConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id', 
        'product_id',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(BuyerSellerMessage::class, 'conversation_id');
    }

    public function participants()
    {
        return collect([$this->buyer, $this->seller])->filter();
    }

    // Helper to get title for admin display
    public function getTitleAttribute()
    {
        $buyerName = $this->buyer?->name ?? 'Unknown';
        $sellerName = $this->seller?->name ?? 'Unknown';
        $productName = $this->product?->name ?? 'General';
        return "Chat: {$buyerName} & {$sellerName} - {$productName}";
    }
}
