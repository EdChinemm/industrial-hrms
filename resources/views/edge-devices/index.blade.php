@extends('layouts.app')

@section('title', 'Edge Devices | Industrial HRMS')

@section('page-heading', 'Edge Devices')

@section(
    'page-description',
    'Attendance terminal monitoring and synchronization'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">

        <div>

            <h2>
                Edge Offline Provisioning
            </h2>

            <p
                style="
                    margin: 5px 0 0;
                    color: #64748b;
                    font-size: 13px;
                "
            >
                Copy active employees, barcodes and
                device details to the local Edge database.
            </p>

        </div>

        <form
            method="POST"
            action="{{ route(
                'edge-devices.provision'
            ) }}"
        >

            @csrf

            <button
                type="submit"
                class="btn btn-primary"
            >
                Refresh Edge Cache
            </button>

        </form>

    </div>

    <div class="section-body">

        <a
            href="{{ route('edge-terminal') }}"
            class="btn btn-secondary"
            target="_blank"
        >
            Open Edge Terminal
        </a>

    </div>

</div>

<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>Registered Edge Devices</h2>
    </div>

    <div class="table-wrap">

        @if ($devices->isEmpty())

            <div class="empty-state">
                No Edge attendance devices have been registered.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Device</th>
                    <th>Device ID</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Pending Records</th>
                    <th>Last Sync</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($devices as $device)

                    <tr>

                        <td>
                            {{ $device->device_name }}
                        </td>

                        <td>
                            {{ $device->device_id }}
                        </td>

                        <td>
                            {{ $device->location ?: '—' }}
                        </td>

                        <td>

                            @if ($device->is_active)

                                <span class="badge badge-success">
                                    Active
                                </span>

                            @else

                                <span class="badge badge-danger">
                                    Inactive
                                </span>

                            @endif

                        </td>

                        <td>

                            <span
                                class="badge
                                {{ $device->pending_records_count > 0
                                    ? 'badge-warning'
                                    : 'badge-success' }}"
                            >
                                {{ $device->pending_records_count }}
                            </span>

                        </td>

                        <td>

                            {{ $device->last_sync_at
                                ? $device->last_sync_at->format('d M Y H:i:s')
                                : 'Never' }}

                        </td>

                        <td>

                            <form
                                method="POST"
                                action="{{ route('edge-devices.sync', $device) }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    {{ !$device->is_active
                                        || $device->pending_records_count === 0
                                            ? 'disabled'
                                            : '' }}
                                >
                                    Synchronize
                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>


<div class="section">

    <div class="section-header">

        <h2>
            Synchronization History
        </h2>

    </div>

    <div class="table-wrap">

        @if ($recentSyncLogs->isEmpty())

            <div class="empty-state">
                No synchronization operations have been performed yet.
            </div>

        @else

            <table>

                <thead>

                <tr>
                    <th>Reference</th>
                    <th>Device</th>
                    <th>Started</th>
                    <th>Processed</th>
                    <th>Synced</th>
                    <th>Failed</th>
                    <th>Success Rate</th>
                    <th>Status</th>
                </tr>

                </thead>

                <tbody>

                @foreach ($recentSyncLogs as $log)

                    <tr>

                        <td>
                            {{ $log->sync_reference }}
                        </td>

                        <td>
                            {{ $log->device->device_name }}
                        </td>

                        <td>
                            {{ $log->sync_started_at->format('d M Y H:i:s') }}
                        </td>

                        <td>
                            {{ $log->records_processed }}
                        </td>

                        <td>
                            {{ $log->records_synced }}
                        </td>

                        <td>
                            {{ $log->records_failed }}
                        </td>

                        <td>
                            {{ number_format($log->success_rate, 2) }}%
                        </td>

                        <td>

                            @if ($log->status === 'completed')

                                <span class="badge badge-success">
                                    Completed
                                </span>

                            @elseif ($log->status === 'failed')

                                <span class="badge badge-danger">
                                    Failed
                                </span>

                            @else

                                <span class="badge badge-warning">
                                    {{ ucfirst($log->status) }}
                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>

@endsection
