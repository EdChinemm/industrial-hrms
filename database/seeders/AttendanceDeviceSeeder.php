<?php

namespace Database\Seeders;

use App\Models\AttendanceDevice;
use Illuminate\Database\Seeder;

class AttendanceDeviceSeeder extends Seeder
{
    public function run(): void
    {
        AttendanceDevice::updateOrCreate(
            [
                'device_id' => 'EDGE-001',
            ],
            [
                'device_name' => 'Edge Attendance Terminal 01',
                'location' => 'Main Entrance',
                'device_type' => 'edge_terminal',
                'ip_address' => '127.0.0.1',
                'mac_address' => null,
                'is_active' => true,
                'description' =>
                    'Prototype Edge attendance terminal used for local attendance capture.',
            ]
        );
    }
}
