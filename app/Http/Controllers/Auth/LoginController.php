<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            // Log failed attempt
            DB::table('login_logs')->insert([
                'user_id' => $user?->id,
                'email' => $credentials['email'],
                'success' => false,
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Invalidate old tokens (optional):
        // $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        // Log success
        DB::table('login_logs')->insert([
            'user_id' => $user->id,
            'email' => $credentials['email'],
            'success' => true,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
