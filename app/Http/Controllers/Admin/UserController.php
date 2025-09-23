<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return User::query()
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->select('users.id','users.name','users.email','users.role','users.created_at','profiles.age','profiles.stage')
            ->orderByDesc('users.id')
            ->limit(50)
            ->get();
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $data = $profile->toArray();
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['role'] = $user->role;
        $data['user_id'] = $user->id;
        return response()->json($data);
    }

    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $data = $request->validate([
            'name' => ['sometimes','string','min:2'],
            'email' => ['sometimes','email','unique:users,email,'.$user->id],
            'age' => ['nullable','integer','min:1','max:120'],
            'stage' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'avatar' => ['nullable','file','image','max:5120'],
            'role' => ['sometimes','in:user,admin'],
        ]);

        if (array_key_exists('name', $data)) $user->name = $data['name'];
        if (array_key_exists('email', $data)) $user->email = $data['email'];
        if (array_key_exists('role', $data)) $user->role = $data['role'];
        $user->save();

        foreach (['age','stage','address','phone'] as $f) {
            if (array_key_exists($f, $data)) { $profile->{$f} = $data[$f]; }
        }
        if ($request->hasFile('avatar')) {
            if ($profile->avatar) Storage::disk('public')->delete($profile->avatar);
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }
        $profile->save();

        $profile->refresh();
        $payload = $profile->toArray();
        $payload['name'] = $user->name;
        $payload['email'] = $user->email;
        $payload['role'] = $user->role;
        $payload['user_id'] = $user->id;
        return response()->json($payload);
    }

    public function resetPassword($id, Request $request)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'password' => ['required','string','min:8','confirmed']
        ]);
        $user->password = Hash::make($data['password']);
        $user->save();
        return response()->json(['success' => true]);
    }
}
