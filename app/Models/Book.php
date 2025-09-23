<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'author',
        'description',
        'file_path',
    ];

    protected $appends = ['file_url'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getFileUrlAttribute(): string
    {
        if (!$this->file_path) return '';
        // Assume files stored on public disk
        return Storage::disk('public')->url($this->file_path);
    }
}
