<?php

namespace Database\Seeders;

use App\Models\PlatformAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlatformAdmin::updateOrCreate(
            ['email' => 'admin@edulink.com'],
            [
                'name' => 'Platform Administrator',
                'password' => Hash::make('password123'),
                'mfa_enabled' => false,
            ]
        );
    }
}
