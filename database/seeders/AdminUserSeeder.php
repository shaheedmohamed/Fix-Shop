<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'mostaql42@gmail.com';
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = new User();
            $user->name = 'Admin';
            $user->email = $email;
        }
        $user->password = Hash::make('shaheed/1234');
        $user->role = 'admin';
        $user->phone = $user->phone ?? null;
        $user->save();
    }
}
