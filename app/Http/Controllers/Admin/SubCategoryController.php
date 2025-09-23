<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index()
    {
        return SubCategory::with('category:id,name')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required','integer','exists:categories,id'],
            'name' => ['required','string','max:255']
        ]);
        $slug = Str::slug($data['name']);
        $exists = SubCategory::where('slug',$slug)->exists();
        if ($exists) { $slug .= '-' . uniqid(); }
        $sub = SubCategory::create(['category_id'=>$data['category_id'], 'name'=>$data['name'], 'slug'=>$slug]);
        return response()->json($sub, 201);
    }

    public function destroy($id)
    {
        $sub = SubCategory::findOrFail($id);
        $sub->delete();
        return response()->json(['success'=>true]);
    }
}
