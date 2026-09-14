<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\EdgeDeviceConfig;
use App\Models\EdgeEmployeeCache;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class EdgeProvisionController extends Controller
{
    /**
     * Refresh the local Edge reference cache.
     *
     * This operation is performed while the central
     * MySQL database is available.
     */
    public function store(): RedirectResponse
    {
        $employees = Employee::with(
            'activeBarcode'
        )
            ->where(
                'employment_status',
                'active'
            )
            ->get()
            ->filter(
                fn ($employee) =>
                    $employee->activeBarcode
                    !== null
            );

        $devices = AttendanceDevice::where(
            'is_active',
            true
        )->get();

        DB::connection('edge')->transaction(
            function () use (
                $employees,
                $devices
            ) {

                /*
                 * Replace the existing cache with a
                 * fresh snapshot from central MySQL.
                 */
                EdgeEmployeeCache::query()
                    ->delete();

                EdgeDeviceConfig::query()
                    ->delete();

                foreach ($employees as $employee) {

                    EdgeEmployeeCache::create([
                        'employee_id' =>
                            $employee->id,

                        'employee_number' =>
                            $employee
                                ->employee_number,

                        'employee_name' =>
                            $employee
                                ->full_name,

                        'barcode' =>
                            $employee
                                ->activeBarcode
                                ->barcode,

                        'is_active' =>
                            true,

                        'cached_at' =>
                            now(),
                    ]);
                }

                foreach ($devices as $device) {

                    EdgeDeviceConfig::create([
                        'device_id' =>
                            $device->device_id,

                        'device_name' =>
                            $device->device_name,

                        'location' =>
                            $device->location,

                        'is_active' =>
                            true,

                        'cached_at' =>
                            now(),
                    ]);
                }
            }
        );

        return back()->with(
            'success',
            'Edge cache refreshed successfully: ' .
            $employees->count() .
            ' employee(s) and ' .
            $devices->count() .
            ' device(s) provisioned.'
        );
    }
}
