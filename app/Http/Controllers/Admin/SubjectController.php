<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
        ]);
        $slug = Str::slug($data['name']);
        // Ensure unique slug
        $base = $slug; $i = 1;
        while (Subject::where('slug', $slug)->exists()) {
            $slug = $base.'-'.($i++);
        }
        $subject = Subject::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);
        return response()->json($subject, 201);
    }
}
