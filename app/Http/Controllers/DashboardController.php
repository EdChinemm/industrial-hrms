<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\View\View;
use App\Models\EdgeAttendanceQueue;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();

        $totalEmployees = Employee::where(
            'employment_status',
            'active'
        )->count();

        $todayPresent = AttendanceRecord::whereDate(
            'attendance_date',
            $today
        )
            ->whereIn('status', [
                'present',
                'late',
                'half_day',
            ])
            ->count();

        $pendingLeaveRequests = LeaveRequest::where(
            'status',
            'pending'
        )->count();

        $activeDevices = AttendanceDevice::where(
            'is_active',
            true
        )->count();

        $pendingSyncRecords = EdgeAttendanceQueue::where(
    'sync_status',
    'pending'
)->count();

        $recentAttendance = AttendanceRecord::with([
            'employee',
            'device',
        ])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalEmployees',
            'todayPresent',
            'pendingLeaveRequests',
            'activeDevices',
            'pendingSyncRecords',
            'recentAttendance'
        ));
    }
}
