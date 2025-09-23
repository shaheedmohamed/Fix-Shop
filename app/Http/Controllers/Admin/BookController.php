<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Subject;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return Book::with('subject:id,name,slug')->orderBy('created_at', 'desc')->paginate(50);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'author' => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'subject_id' => ['required','exists:subjects,id'],
            'file' => ['required','file','mimes:pdf','max:25600'], // 25MB
        ]);

        $path = $request->file('file')->store('books', 'public');

        $book = Book::create([
            'subject_id' => $data['subject_id'],
            'title' => $data['title'],
            'author' => $data['author'] ?? null,
            'description' => $data['description'] ?? null,
            'file_path' => $path,
        ]);

        return response()->json($book->load('subject:id,name,slug'), 201);
    }
}
