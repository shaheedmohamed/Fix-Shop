<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'age', 'stage', 'address', 'phone', 'avatar',
        'reminders_enabled', 'reminder_time'
    ];

    protected $appends = ['avatar_url'];

    protected $casts = [
        'reminders_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) return '';
        return Storage::disk('public')->url($this->avatar);
    }
}
