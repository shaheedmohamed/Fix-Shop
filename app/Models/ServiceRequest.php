<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'title',
        'description',
        'budget',
        'location',
        'deadline',
        'status',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'deadline' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offers()
    {
        return $this->hasMany(ServiceOffer::class);
    }
}
