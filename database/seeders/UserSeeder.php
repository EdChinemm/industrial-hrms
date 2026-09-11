<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $adminRole = Role::where('name', 'System Administrator')
        ->firstOrFail();

    $hrRole = Role::where('name', 'HR Officer')
        ->firstOrFail();

    User::updateOrCreate(
        [
            'email' => 'admin@industrial-hrms.local',
        ],
        [
            'name' => 'System Administrator',
            'role_id' => $adminRole->id,
            'password' => Hash::make('Admin@12345'),
            'email_verified_at' => now(),
        ]
    );

    User::updateOrCreate(
        [
            'email' => 'hr@industrial-hrms.local',
        ],
        [
            'name' => 'HR Officer',
            'role_id' => $hrRole->id,
            'password' => Hash::make('Hr@12345'),
            'email_verified_at' => now(),
        ]
    );
}
}
