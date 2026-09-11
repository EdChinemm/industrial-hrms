<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Administration',
                'code' => 'ADMIN',
                'description' => 'Handles general administration and organizational support.',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Handles employee administration and human resource activities.',
                'is_active' => true,
            ],
            [
                'name' => 'Production',
                'code' => 'PROD',
                'description' => 'Handles industrial production and operational activities.',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MAINT',
                'description' => 'Handles equipment, machinery, electrical, and facility maintenance.',
                'is_active' => true,
            ],
            [
                'name' => 'Stores and Inventory',
                'code' => 'STORES',
                'description' => 'Handles materials, stock, supplies, and inventory control.',
                'is_active' => true,
            ],
            [
                'name' => 'Health and Safety',
                'code' => 'HSE',
                'description' => 'Handles workplace health, safety, and compliance activities.',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}
