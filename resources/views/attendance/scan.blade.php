@extends('layouts.app')

@section('title', 'Attendance | Industrial HRMS')

@section('page-heading', 'Edge Attendance')

@section(
    'page-description',
    'Local barcode capture with Edge-to-central synchronization'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>Scan Employee Barcode</h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route('attendance.scan.store') }}"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label for="attendance_device_id">
                        Edge Device *
                    </label>

                    <select
                        class="form-control"
                        id="attendance_device_id"
                        name="attendance_device_id"
                        required
                    >

                        <option value="">
                            Select Edge Device
                        </option>

                        @foreach ($devices as $device)

                            <option
                                value="{{ $device->id }}"
                                @selected(
                                    old('attendance_device_id') == $device->id
                                )
                            >
                                {{ $device->device_name }}
                                — {{ $device->location }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label for="barcode">
                        Employee Barcode *
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        id="barcode"
                        name="barcode"
                        value="{{ old('barcode') }}"
                        placeholder="Scan or enter barcode"
                        autocomplete="off"
                        autofocus
                        required
                    >

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Process Attendance
                </button>

            </div>

        </form>

    </div>

</div>

<div class="section">

    <div class="section-header">
        <h2>Today's Attendance</h2>
    </div>

    <div class="table-wrap">

        @if ($todayRecords->isEmpty())

            <div class="empty-state">
                No attendance has been recorded today.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Device</th>
                    <th>Sync</th>
                </tr>
                </thead>

                <tbody>

@foreach ($todayRecords as $record)

    <tr>

        <td>
            {{ $record->employee_name }}
        </td>

        <td>
            {{ $record->check_in?->format('H:i:s') ?? '—' }}
        </td>

        <td>
            {{ $record->check_out?->format('H:i:s') ?? '—' }}
        </td>

        <td>
            <span class="badge badge-success">
                {{ ucfirst($record->status) }}
            </span>
        </td>

        <td>
            {{ $record->device_id }}
        </td>

        <td>

            <span
                class="badge
                {{ $record->sync_status === 'synced'
                    ? 'badge-success'
                    : 'badge-warning' }}"
            >
                {{ ucfirst($record->sync_status) }}
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
