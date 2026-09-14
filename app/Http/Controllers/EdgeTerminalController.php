<?php

namespace App\Http\Controllers;

use App\Models\EdgeAttendanceQueue;
use App\Models\EdgeDeviceConfig;
use App\Models\EdgeEmployeeCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EdgeTerminalController extends Controller
{
    /**
     * Display the standalone local Edge terminal.
     *
     * This page only uses SQLite.
     */
    public function create(): View
    {
        $device = EdgeDeviceConfig::where(
            'is_active',
            true
        )->first();

        $cachedEmployees =
            EdgeEmployeeCache::where(
                'is_active',
                true
            )->count();

        $pendingRecords =
            EdgeAttendanceQueue::whereIn(
                'sync_status',
                [
                    'pending',
                    'failed',
                ]
            )->count();

        $todayRecords =
            EdgeAttendanceQueue::whereDate(
                'attendance_date',
                today()
            )
                ->latest(
                    'updated_at'
                )
                ->limit(15)
                ->get();

        return view(
            'edge-terminal.index',
            compact(
                'device',
                'cachedEmployees',
                'pendingRecords',
                'todayRecords'
            )
        );
    }

    /**
     * Capture attendance using only the local
     * Edge SQLite database.
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

            'device_id' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Local Edge Device
        |--------------------------------------------------------------------------
        */

        $device = EdgeDeviceConfig::where(
            'device_id',
            $validated['device_id']
        )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (!$device) {
            return back()->withErrors([
                'device' =>
                    'The local Edge terminal has not been provisioned or is inactive.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Barcode Using Local SQLite Cache
        |--------------------------------------------------------------------------
        */

        $employee =
            EdgeEmployeeCache::where(
                'barcode',
                trim(
                    $validated['barcode']
                )
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();

        if (!$employee) {
            return back()
                ->withErrors([
                    'barcode' =>
                        'Invalid or unavailable local employee barcode.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Find Today's Local Attendance Session
        |--------------------------------------------------------------------------
        */

        $attendance =
            EdgeAttendanceQueue::where(
                'employee_id',
                $employee->employee_id
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
                    now()->format(
                        'YmdHis'
                    ) .
                    '-' .
                    strtoupper(
                        Str::random(6)
                    ),

                'employee_id' =>
                    $employee
                        ->employee_id,

                'employee_number' =>
                    $employee
                        ->employee_number,

                'employee_name' =>
                    $employee
                        ->employee_name,

                'barcode' =>
                    $employee
                        ->barcode,

                'device_id' =>
                    $device
                        ->device_id,

                'attendance_date' =>
                    today(),

                'check_in' =>
                    now(),

                'check_out' =>
                    null,

                'status' =>
                    'present',

                'sync_status' =>
                    'pending',

                'synced_at' =>
                    null,

                'sync_error' =>
                    null,
            ]);

            return back()->with(
                'success',
                $employee->employee_name .
                ' checked in successfully on the local Edge terminal.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Second Scan = Check Out
        |--------------------------------------------------------------------------
        */

        if (!$attendance->check_out) {

            $attendance->update([
                'check_out' =>
                    now(),

                'sync_status' =>
                    'pending',

                'synced_at' =>
                    null,

                'sync_error' =>
                    null,
            ]);

            return back()->with(
                'success',
                $employee->employee_name .
                ' checked out successfully on the local Edge terminal.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Already Completed
        |--------------------------------------------------------------------------
        */

        return back()->withErrors([
            'barcode' =>
                'Attendance for this employee has already been completed today.',
        ]);
    }
}
