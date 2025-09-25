<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerSellerMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body'
    ];

    public function conversation()
    {
        return $this->belongsTo(BuyerSellerConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Alias for compatibility with existing message system
    public function getContentAttribute()
    {
        return $this->body;
    }
}
