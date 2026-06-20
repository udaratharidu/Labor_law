<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Seed a known test user for local development and manual QA.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'user@ludexora.live'],
            [
                'name' => 'Ludexora User',
                'password' => Hash::make('Ludex.2021t@ora'),
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'client@ludexora.live'],
            [
                'name' => 'Ludexora Client',
                'password' => Hash::make('Ludex.2021t@ora'),
                'email_verified_at' => now(),
            ]
        );
    }
}
