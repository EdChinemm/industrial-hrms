<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSyncLog;
use App\Models\EdgeAttendanceQueue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AttendanceSyncController extends Controller
{
    /**
     * Synchronize local Edge attendance records
     * from SQLite to the central MySQL database.
     */
    public function store(
        AttendanceDevice $attendanceDevice
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Validate Device
        |--------------------------------------------------------------------------
        */

        if (!$attendanceDevice->is_active) {
            return back()->withErrors([
                'sync' => 'Synchronization cannot be performed because the Edge device is inactive.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Retrieve Unsynchronized Local Records
        |--------------------------------------------------------------------------
        */

        $edgeRecords = EdgeAttendanceQueue::where(
            'device_id',
            $attendanceDevice->device_id
        )
            ->whereIn('sync_status', [
                'pending',
                'failed',
            ])
            ->orderBy('id')
            ->get();

        $recordsToProcess = $edgeRecords->count();

        if ($recordsToProcess === 0) {
            return back()->with(
                'success',
                'There are no unsynchronized Edge attendance records for this device.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Synchronization Reference
        |--------------------------------------------------------------------------
        */

        $syncReference =
            'SYNC-' .
            $attendanceDevice->device_id .
            '-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(Str::random(4));

        /*
        |--------------------------------------------------------------------------
        | Create Central Synchronization Log
        |--------------------------------------------------------------------------
        */

        $syncLog = AttendanceSyncLog::create([
            'attendance_device_id' => $attendanceDevice->id,
            'sync_reference' => $syncReference,
            'sync_started_at' => now(),
            'sync_completed_at' => null,
            'records_processed' => $recordsToProcess,
            'records_synced' => 0,
            'records_failed' => 0,
            'status' => 'processing',
            'error_message' => null,
        ]);

        $synced = 0;
        $failed = 0;
        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | Synchronize Each Local Record
        |--------------------------------------------------------------------------
        */

        foreach ($edgeRecords as $edgeRecord) {
            try {
                $syncedAt = now();

                DB::connection('mysql')->transaction(
                    function () use (
                        $edgeRecord,
                        $attendanceDevice,
                        $syncedAt
                    ) {
                        AttendanceRecord::updateOrCreate(
                            [
                                'employee_id' =>
                                    $edgeRecord->employee_id,

                                'attendance_date' =>
                                    $edgeRecord->attendance_date->toDateString(),
                            ],
                            [
                                'check_in' =>
                                    $edgeRecord->check_in,

                                'check_out' =>
                                    $edgeRecord->check_out,

                                'status' =>
                                    $edgeRecord->status,

                                'attendance_source' =>
                                    'edge_device',

                                'device_id' =>
                                    $attendanceDevice->device_id,

                                'sync_status' =>
                                    'synced',

                                'synced_at' =>
                                    $syncedAt,

                                'remarks' =>
                                    'Synchronized from local Edge terminal.',
                            ]
                        );
                    }
                );

                /*
                 * Only mark the Edge record as synced
                 * after central MySQL succeeds.
                 */
                $edgeRecord->update([
                    'sync_status' => 'synced',
                    'synced_at' => $syncedAt,
                    'sync_error' => null,
                ]);

                $synced++;
            } catch (Throwable $exception) {
                $failed++;

                $message = mb_substr(
                    $exception->getMessage(),
                    0,
                    500
                );

                $edgeRecord->update([
                    'sync_status' => 'failed',
                    'synced_at' => null,
                    'sync_error' => $message,
                ]);

                $errors[] =
                    $edgeRecord->local_reference .
                    ': ' .
                    $message;

                report($exception);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update Device Last Synchronization
        |--------------------------------------------------------------------------
        */

        if ($synced > 0) {
            $attendanceDevice->update([
                'last_sync_at' => now(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Final Status
        |--------------------------------------------------------------------------
        */

        if ($failed === 0) {
            $finalStatus = 'completed';
        } elseif ($synced === 0) {
            $finalStatus = 'failed';
        } else {
            $finalStatus = 'partial';
        }

        /*
        |--------------------------------------------------------------------------
        | Finalize Synchronization Log
        |--------------------------------------------------------------------------
        */

        $syncLog->update([
            'sync_completed_at' => now(),
            'records_processed' => $recordsToProcess,
            'records_synced' => $synced,
            'records_failed' => $failed,
            'status' => $finalStatus,
            'error_message' => empty($errors)
                ? null
                : implode(PHP_EOL, $errors),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        if ($failed > 0) {
            return back()->withErrors([
                'sync' =>
                    "{$synced} record(s) synchronized successfully and {$failed} record(s) failed.",
            ]);
        }

        return back()->with(
            'success',
            "{$synced} Edge attendance record(s) synchronized successfully."
        );
    }
}
