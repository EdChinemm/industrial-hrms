<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'department_code' => 'ADMIN',
                'title' => 'Administrative Officer',
                'code' => 'ADMIN-OFF',
                'description' => 'Provides administrative and organizational support.',
            ],

            [
                'department_code' => 'HR',
                'title' => 'HR Manager',
                'code' => 'HR-MGR',
                'description' => 'Oversees human resource operations.',
            ],
            [
                'department_code' => 'HR',
                'title' => 'HR Officer',
                'code' => 'HR-OFF',
                'description' => 'Handles employee records and HR administration.',
            ],

            [
                'department_code' => 'PROD',
                'title' => 'Production Supervisor',
                'code' => 'PROD-SUP',
                'description' => 'Supervises industrial production activities.',
            ],
            [
                'department_code' => 'PROD',
                'title' => 'Machine Operator',
                'code' => 'PROD-OP',
                'description' => 'Operates production machinery and equipment.',
            ],
            [
                'department_code' => 'PROD',
                'title' => 'General Worker',
                'code' => 'PROD-GW',
                'description' => 'Performs general production and operational duties.',
            ],

            [
                'department_code' => 'MAINT',
                'title' => 'Maintenance Technician',
                'code' => 'MAINT-TECH',
                'description' => 'Performs machinery and equipment maintenance.',
            ],
            [
                'department_code' => 'MAINT',
                'title' => 'Electrician',
                'code' => 'MAINT-ELEC',
                'description' => 'Performs electrical installation and maintenance work.',
            ],

            [
                'department_code' => 'STORES',
                'title' => 'Storekeeper',
                'code' => 'STORE-KPR',
                'description' => 'Manages materials and stock held in stores.',
            ],
            [
                'department_code' => 'STORES',
                'title' => 'Inventory Clerk',
                'code' => 'INV-CLK',
                'description' => 'Maintains inventory and stock records.',
            ],

            [
                'department_code' => 'HSE',
                'title' => 'Safety Officer',
                'code' => 'HSE-OFF',
                'description' => 'Monitors workplace health and safety practices.',
            ],
        ];

        foreach ($positions as $positionData) {

            $department = Department::where(
                'code',
                $positionData['department_code']
            )->firstOrFail();

            Position::updateOrCreate(
                ['code' => $positionData['code']],
                [
                    'department_id' => $department->id,
                    'title' => $positionData['title'],
                    'description' => $positionData['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
