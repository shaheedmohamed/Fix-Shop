<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIMessage extends Model
{
    use HasFactory;

    protected $fillable = ['ai_conversation_id', 'role', 'content'];

    public function conversation(){ return $this->belongsTo(AIConversation::class, 'ai_conversation_id'); }
}
