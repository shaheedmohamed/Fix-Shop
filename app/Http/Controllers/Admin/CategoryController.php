<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::query()->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255']
        ]);
        $slug = Str::slug($data['name']);
        $exists = Category::where('slug',$slug)->exists();
        if ($exists) { $slug .= '-' . uniqid(); }
        $cat = Category::create(['name'=>$data['name'], 'slug'=>$slug]);
        return response()->json($cat, 201);
    }

    public function destroy($id)
    {
        $cat = Category::findOrFail($id);
        $cat->delete();
        return response()->json(['success'=>true]);
    }
}
