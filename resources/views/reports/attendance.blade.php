@extends('layouts.app')

@section('title', 'Attendance Reports | Industrial HRMS')

@section('page-heading', 'Attendance Reports')

@section(
    'page-description',
    'Central attendance history, monitoring and reporting'
)

@section('content')

<div class="grid">

    <div class="card">
        <span class="stat-label">Total Records</span>
        <strong class="stat-value">
            {{ $totalRecords }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">Present</span>
        <strong class="stat-value">
            {{ $presentCount }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">Late</span>
        <strong class="stat-value">
            {{ $lateCount }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">Absent</span>
        <strong class="stat-value">
            {{ $absentCount }}
        </strong>
    </div>

</div>


<div class="section">

    <div class="section-header">

        <h2>Report Filters</h2>

        <a
            href="{{ route(
                'reports.attendance.export',
                request()->query()
            ) }}"
            class="btn btn-primary"
        >
            Export CSV
        </a>

    </div>

    <div class="section-body">

        <form
            method="GET"
            action="{{ route('reports.attendance') }}"
        >

            <div class="form-grid">

                <div class="form-group">

                    <label>Date From</label>

                    <input
                        class="form-control"
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                    >

                </div>

                <div class="form-group">

                    <label>Date To</label>

                    <input
                        class="form-control"
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                    >

                </div>

                <div class="form-group">

                    <label>Employee</label>

                    <select
                        class="form-control"
                        name="employee_id"
                    >

                        <option value="">
                            All Employees
                        </option>

                        @foreach ($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    request('employee_id')
                                    == $employee->id
                                )
                            >
                                {{ $employee->employee_number }}
                                —
                                {{ $employee->full_name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select
                        class="form-control"
                        name="status"
                    >

                        <option value="">
                            All Statuses
                        </option>

                        @foreach ([
                            'present',
                            'absent',
                            'late',
                            'half_day',
                            'on_leave',
                            'holiday'
                        ] as $status)

                            <option
                                value="{{ $status }}"
                                @selected(
                                    request('status')
                                    === $status
                                )
                            >
                                {{ ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $status
                                    )
                                ) }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Synchronization</label>

                    <select
                        class="form-control"
                        name="sync_status"
                    >

                        <option value="">
                            All
                        </option>

                        <option
                            value="synced"
                            @selected(
                                request('sync_status')
                                === 'synced'
                            )
                        >
                            Synced
                        </option>

                        <option
                            value="pending"
                            @selected(
                                request('sync_status')
                                === 'pending'
                            )
                        >
                            Pending
                        </option>

                    </select>

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Apply Filters
                </button>

                <a
                    href="{{ route('reports.attendance') }}"
                    class="btn btn-secondary"
                >
                    Clear
                </a>

            </div>

        </form>

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Attendance History</h2>
    </div>

    <div class="table-wrap">

        @if ($attendanceRecords->isEmpty())

            <div class="empty-state">
                No attendance records match the selected filters.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Department</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Hours</th>
                    <th>Status</th>
                    <th>Device</th>
                    <th>Sync</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($attendanceRecords as $record)

                    <tr>

                        <td>
                            {{ $record->employee->full_name }}
                        </td>

                        <td>
                            {{ $record->attendance_date
                                ->format('d M Y') }}
                        </td>

                        <td>
                            {{ $record->employee
                                ->department->name }}
                        </td>

                        <td>
                            {{ $record->check_in
                                ?->format('H:i:s') ?? '—' }}
                        </td>

                        <td>
                            {{ $record->check_out
                                ?->format('H:i:s') ?? '—' }}
                        </td>

                        <td>
                            {{ number_format(
                                $record->hours_worked,
                                2
                            ) }}
                        </td>

                        <td>
                            <span class="badge">
                                {{ ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $record->status
                                    )
                                ) }}
                            </span>
                        </td>

                        <td>
                            {{ $record->device
                                ?->device_name
                                ?? $record->device_id
                                ?? '—' }}
                        </td>

                        <td>

                            <span
                                class="badge
                                {{ $record->sync_status
                                    === 'synced'
                                        ? 'badge-success'
                                        : 'badge-warning' }}"
                            >
                                {{ ucfirst(
                                    $record->sync_status
                                ) }}
                            </span>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <div class="section-body">
                {{ $attendanceRecords->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
