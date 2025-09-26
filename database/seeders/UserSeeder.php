<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $total = 100;
        $providers = (int) floor($total / 2); // 50 providers
        $buyers = $total - $providers;        // 50 buyers

        // Create buyers
        for ($i = 1; $i <= $buyers; $i++) {
            User::firstOrCreate(
                ['email' => "buyer{$i}@example.com"],
                [
                    'name' => 'Buyer '.$i,
                    'password' => Hash::make('password'),
                    'phone' => '010'.str_pad((string)rand(10000000,99999999), 8, '0', STR_PAD_LEFT),
                    'role' => 'buyer',
                ]
            );
        }

        // Create providers (sellers)
        for ($i = 1; $i <= $providers; $i++) {
            User::firstOrCreate(
                ['email' => "provider{$i}@example.com"],
                [
                    'name' => 'Provider '.$i,
                    'password' => Hash::make('password'),
                    'phone' => '011'.str_pad((string)rand(10000000,99999999), 8, '0', STR_PAD_LEFT),
                    'role' => 'provider',
                    'store_name' => 'Store '.$i,
                    'merchant_name' => 'Merchant '.$i,
                    'service_type' => 'products',
                    'logo_path' => null,
                ]
            );
        }
    }
}
