<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ParentController extends Controller
{
    public function children(Request $request)
    {
        $user = $request->user();
        return Child::query()
            ->where('parent_id', $user->id)
            ->orderByDesc('id')
            ->get();
    }

    public function addChild(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'parent') {
            return response()->json(['message' => 'Only parents can add children'], 403);
        }
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'age' => ['nullable','integer','min:3','max:20'],
        ]);
        $childUserId = null;
        if (!empty($data['email'])) {
            $existing = User::where('email', $data['email'])->first();
            if ($existing && $existing->id !== $user->id) {
                $childUserId = $existing->id;
            }
        }
        $child = Child::create([
            'parent_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'age' => $data['age'] ?? null,
            'child_user_id' => $childUserId,
        ]);
        return response()->json($child, 201);
    }

    public function removeChild(Request $request, $id)
    {
        $user = $request->user();
        $child = Child::where('parent_id', $user->id)->where('id', $id)->first();
        if (!$child) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $child->delete();
        return response()->json(['ok' => true]);
    }
}
