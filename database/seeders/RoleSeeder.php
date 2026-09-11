<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'System Administrator',
                'description' => 'Manages system configuration, users, roles, devices, and overall administration.',
                'is_active' => true,
            ],
            [
                'name' => 'HR Officer',
                'description' => 'Manages employee records, attendance, leave, reports, and HR-related operations.',
                'is_active' => true,
            ],
            [
                'name' => 'Supervisor',
                'description' => 'Monitors employee attendance and reviews operational workforce information.',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
