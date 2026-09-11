<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\EdgeAttendanceQueue;
use App\Models\EmployeeBarcode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceScanController extends Controller
{
    /**
     * Display the Edge attendance scanning page.
     */
    public function create(): View
    {
        $devices = AttendanceDevice::where(
            'is_active',
            true
        )
            ->orderBy('device_name')
            ->get();

        $todayRecords = EdgeAttendanceQueue::whereDate(
            'attendance_date',
            today()
        )
            ->latest('updated_at')
            ->limit(15)
            ->get();

        return view(
            'attendance.scan',
            compact(
                'devices',
                'todayRecords'
            )
        );
    }

    /**
     * Capture attendance locally at the Edge terminal.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'barcode' => [
                'required',
                'string',
                'max:100',
            ],

            'attendance_device_id' => [
                'required',
                'integer',
                'exists:attendance_devices,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Edge Device
        |--------------------------------------------------------------------------
        */

        $device = AttendanceDevice::whereKey(
            $validated['attendance_device_id']
        )
            ->where('is_active', true)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Validate Employee Barcode
        |--------------------------------------------------------------------------
        */

        $employeeBarcode = EmployeeBarcode::with(
            'employee'
        )
            ->where(
                'barcode',
                trim($validated['barcode'])
            )
            ->where('is_active', true)
            ->first();

        if (!$employeeBarcode) {
            return back()
                ->withErrors([
                    'barcode' =>
                        'Invalid or inactive employee barcode.',
                ])
                ->withInput();
        }

        $employee = $employeeBarcode->employee;

        if ($employee->employment_status !== 'active') {
            return back()->withErrors([
                'barcode' =>
                    'This employee is not currently active.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Local Edge Queue
        |--------------------------------------------------------------------------
        */

        $attendance = EdgeAttendanceQueue::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'attendance_date',
                today()
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | First Scan = Check In
        |--------------------------------------------------------------------------
        */

        if (!$attendance) {
            EdgeAttendanceQueue::create([
                'local_reference' =>
                    'EDGE-' .
                    now()->format('YmdHis') .
                    '-' .
                    strtoupper(Str::random(6)),

                'employee_id' =>
                    $employee->id,

                'employee_number' =>
                    $employee->employee_number,

                'employee_name' =>
                    $employee->full_name,

                'barcode' =>
                    $employeeBarcode->barcode,

                'device_id' =>
                    $device->device_id,

                'attendance_date' =>
                    today(),

                'check_in' =>
                    now(),

                'status' =>
                    'present',

                'sync_status' =>
                    'pending',
            ]);

            return back()->with(
                'success',
                $employee->full_name .
                ' checked in locally at the Edge terminal.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Second Scan = Check Out
        |--------------------------------------------------------------------------
        */

        if (!$attendance->check_out) {
            $attendance->update([
                'check_out' => now(),
                'sync_status' => 'pending',
                'synced_at' => null,
                'sync_error' => null,
            ]);

            return back()->with(
                'success',
                $employee->full_name .
                ' checked out locally at the Edge terminal.'
            );
        }

        return back()->withErrors([
            'barcode' =>
                'Attendance for this employee has already been completed today.',
        ]);
    }
}
