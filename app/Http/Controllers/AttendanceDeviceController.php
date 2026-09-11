<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\AttendanceSyncLog;
use App\Models\EdgeAttendanceQueue;
use Illuminate\View\View;

class AttendanceDeviceController extends Controller
{
    public function index(): View
    {
        $devices = AttendanceDevice::orderBy(
            'device_name'
        )->get();

        foreach ($devices as $device) {

            $device->pending_records_count =
                EdgeAttendanceQueue::where(
                    'device_id',
                    $device->device_id
                )
                    ->where(
                        'sync_status',
                        'pending'
                    )
                    ->count();
        }

        $recentSyncLogs = AttendanceSyncLog::with(
            'device'
        )
            ->latest('sync_started_at')
            ->limit(10)
            ->get();

        return view(
            'edge-devices.index',
            compact(
                'devices',
                'recentSyncLogs'
            )
        );
    }
}
