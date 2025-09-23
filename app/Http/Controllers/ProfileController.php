<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        // include user name/email in payload
        $data = $profile->toArray();
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $data = $request->validate([
            'name' => ['sometimes','string','min:2'],
            'email' => ['sometimes','email','unique:users,email,'.$user->id],
            'age' => ['nullable','integer','min:1','max:120'],
            'stage' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'reminders_enabled' => ['sometimes','boolean'],
            'reminder_time' => ['nullable','string','max:5'],
            'avatar' => ['nullable','file','image','max:5120'], // 5 MB
        ]);

        if (array_key_exists('name', $data)) { $user->name = $data['name']; }
        if (array_key_exists('email', $data)) { $user->email = $data['email']; }
        $user->save();

        // update profile fields
        foreach (['age','stage','address','phone','reminders_enabled','reminder_time'] as $f) {
            if (array_key_exists($f, $data)) { $profile->{$f} = $data[$f]; }
        }

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }
        $profile->save();

        $profile->refresh();
        $payload = $profile->toArray();
        $payload['name'] = $user->name;
        $payload['email'] = $user->email;
        return response()->json($payload);
    }
}
