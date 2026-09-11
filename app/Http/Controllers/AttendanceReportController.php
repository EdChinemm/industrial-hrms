<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = AttendanceRecord::with([
            'employee.department',
            'employee.position',
            'device',
        ]);

        $this->applyFilters($query, $request);

        $totalRecords = (clone $query)->count();

        $presentCount = (clone $query)
            ->where('status', 'present')
            ->count();

        $lateCount = (clone $query)
            ->where('status', 'late')
            ->count();

        $absentCount = (clone $query)
            ->where('status', 'absent')
            ->count();

        $attendanceRecords = (clone $query)
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in')
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::where(
            'employment_status',
            'active'
        )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'reports.attendance',
            compact(
                'attendanceRecords',
                'employees',
                'totalRecords',
                'presentCount',
                'lateCount',
                'absentCount'
            )
        );
    }

    public function export(Request $request): StreamedResponse
    {
        $query = AttendanceRecord::with([
            'employee.department',
            'employee.position',
            'device',
        ]);

        $this->applyFilters($query, $request);

        $records = $query
            ->orderBy('attendance_date')
            ->get();

        $fileName =
            'attendance-report-' .
            now()->format('Ymd-His') .
            '.csv';

        return response()->streamDownload(
            function () use ($records) {

                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Employee Number',
                    'Employee Name',
                    'Department',
                    'Position',
                    'Attendance Date',
                    'Check In',
                    'Check Out',
                    'Hours Worked',
                    'Status',
                    'Device',
                    'Sync Status',
                ]);

                foreach ($records as $record) {
                    fputcsv($handle, [
                        $record->employee->employee_number,
                        $record->employee->full_name,
                        $record->employee->department->name,
                        $record->employee->position->title,
                        $record->attendance_date->format('Y-m-d'),
                        $record->check_in?->format('H:i:s'),
                        $record->check_out?->format('H:i:s'),
                        $record->hours_worked,
                        $record->status,
                        $record->device?->device_name
                            ?? $record->device_id,
                        $record->sync_status,
                    ]);
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    private function applyFilters(
        Builder $query,
        Request $request
    ): void {
        if ($request->filled('date_from')) {
            $query->whereDate(
                'attendance_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'attendance_date',
                '<=',
                $request->date_to
            );
        }

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('sync_status')) {
            $query->where(
                'sync_status',
                $request->sync_status
            );
        }
    }
}
