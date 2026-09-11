@extends('layouts.app')

@section('title', 'Dashboard | Industrial HRMS')

@section('page-heading', 'Dashboard')

@section(
    'page-description',
    'Attendance, workforce and Edge synchronization overview'
)

@section('content')

<div class="grid">

    <div class="card">
        <span class="stat-label">
            Active Employees
        </span>

        <strong class="stat-value">
            {{ $totalEmployees }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">
            Present Today
        </span>

        <strong class="stat-value">
            {{ $todayPresent }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">
            Pending Leave
        </span>

        <strong class="stat-value">
            {{ $pendingLeaveRequests }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">
            Active Edge Devices
        </span>

        <strong class="stat-value">
            {{ $activeDevices }}
        </strong>
    </div>

</div>

<div class="section">

    <div class="section-header">

        <h2>
            Edge Synchronization
        </h2>

        <span class="badge {{ $pendingSyncRecords > 0 ? 'badge-warning' : 'badge-success' }}">
            {{ $pendingSyncRecords }} Pending
        </span>

    </div>

    <div class="section-body">

        <p>
            Attendance records waiting for synchronization:
            <strong>{{ $pendingSyncRecords }}</strong>
        </p>

    </div>

</div>

<div class="section">

    <div class="section-header">
        <h2>Recent Attendance</h2>
    </div>

    <div class="table-wrap">

        @if ($recentAttendance->isEmpty())

            <div class="empty-state">
                No attendance records have been captured yet.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Device</th>
                    <th>Sync</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($recentAttendance as $attendance)

                    <tr>

                        <td>
                            {{ $attendance->employee->full_name }}
                        </td>

                        <td>
                            {{ $attendance->attendance_date->format('d M Y') }}
                        </td>

                        <td>
                            {{ $attendance->check_in?->format('H:i') ?? '—' }}
                        </td>

                        <td>
                            {{ $attendance->check_out?->format('H:i') ?? '—' }}
                        </td>

                        <td>
                            <span class="badge">
                                {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                            </span>
                        </td>

                        <td>
                            {{ $attendance->device?->device_name ?? $attendance->device_id ?? '—' }}
                        </td>

                        <td>
                            <span class="badge {{ $attendance->is_synced ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($attendance->sync_status) }}
                            </span>
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>

@endsection
