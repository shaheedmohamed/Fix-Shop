<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Stream PDF inline; works even without storage symlink
    public function file($id)
    {
        $book = Book::findOrFail($id);
        if (!$book->file_path) {
            abort(404);
        }
        $disk = Storage::disk('public');
        if (!$disk->exists($book->file_path)) {
            abort(404);
        }
        $path = $disk->path($book->file_path);
        // Force inline display for PDFs
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"'
        ]);
    }
}
