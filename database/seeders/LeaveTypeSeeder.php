<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'ANNUAL',
                'description' => 'Planned annual leave for eligible employees.',
                'default_days' => 24,
                'is_paid' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SICK',
                'description' => 'Leave granted when an employee is unable to work due to illness.',
                'default_days' => 10,
                'is_paid' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Compassionate Leave',
                'code' => 'COMP',
                'description' => 'Leave granted for serious family or compassionate circumstances.',
                'default_days' => 5,
                'is_paid' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'MAT',
                'description' => 'Leave associated with maternity circumstances.',
                'default_days' => 90,
                'is_paid' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PAT',
                'description' => 'Leave associated with paternity circumstances.',
                'default_days' => 5,
                'is_paid' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UNPAID',
                'description' => 'Approved leave taken without normal earnings.',
                'default_days' => 0,
                'is_paid' => false,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::updateOrCreate(
                ['code' => $leaveType['code']],
                $leaveType
            );
        }
    }
}
