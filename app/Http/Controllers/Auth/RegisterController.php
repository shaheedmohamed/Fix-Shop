<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Determine role: buyer (default) or provider
        $role = in_array($request->input('role'), ['buyer','provider']) ? $request->input('role') : 'buyer';

        // Common validations
        $baseRules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($role === 'buyer') {
            $rules = array_merge($baseRules, [
                'name' => ['required', 'string', 'min:3'],
                'phone' => ['required', 'string'],
            ]);
        } else { // provider
            $rules = array_merge($baseRules, [
                'merchant_name' => ['required', 'string', 'min:3'],
                'store_name' => ['required', 'string', 'min:2'],
                'service_type' => ['required', 'string', 'min:3'],
                'phone' => ['required', 'string'],
                'logo' => ['nullable', 'file', 'image', 'max:2048'],
            ]);
        }

        $data = $request->validate($rules);

        // Handle optional logo upload for providers
        $logoPath = null;
        if ($role === 'provider' && $request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $user = User::create([
            'name' => $role === 'buyer' ? $data['name'] : $data['merchant_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $role,
            'store_name' => $role === 'provider' ? ($data['store_name'] ?? null) : null,
            'merchant_name' => $role === 'provider' ? ($data['merchant_name'] ?? null) : null,
            'service_type' => $role === 'provider' ? ($data['service_type'] ?? null) : null,
            'logo_path' => $logoPath,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully',
            'user' => $user,
            'token' => $token,
            'redirect' => $role === 'provider' ? '/dashboard' : '/products',
        ], 201);
    }
}

