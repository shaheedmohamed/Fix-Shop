<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $items = Post::where('user_id', $user->id)->orderByDesc('id')->get();
        $items->transform(function($p){
            if ($p->image_path) {
                $p->image_path = Storage::disk('public')->url($p->image_path);
            }
            return $p;
        });
        return $items;
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $data = $request->validate([
            'content' => ['required','string','max:5000'],
            'image' => ['nullable','image','max:4096']
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }
        $post = Post::create([
            'user_id' => $user->id,
            'content' => $data['content'],
            'image_path' => $imagePath,
        ]);
        return response()->json($post, 201);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $post = Post::where('user_id', $user->id)->findOrFail($id);
        if ($post->image_path) Storage::disk('public')->delete($post->image_path);
        $post->delete();
        return response()->json(['success'=>true]);
    }
}
