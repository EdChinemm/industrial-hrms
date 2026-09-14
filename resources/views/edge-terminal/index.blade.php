<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edge Attendance Terminal
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background: #eef2f7;
            color: #172033;
        }

        .page {
            max-width: 1050px;
            margin: 0 auto;
            padding: 30px 20px 60px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .subtitle {
            margin-top: 6px;
            color: #64748b;
        }

        .offline-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 13px;
            font-weight: 700;
        }

        .grid {
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );
            gap: 16px;
            margin-bottom: 22px;
        }

        .card,
        .panel {
            background: white;
            border: 1px solid #dbe1e8;
            border-radius: 12px;
        }

        .card {
            padding: 20px;
        }

        .label {
            display: block;
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .value {
            font-size: 22px;
            font-weight: 700;
        }

        .panel {
            margin-bottom: 22px;
        }

        .panel-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .panel-body {
            padding: 22px;
        }

        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 8px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 14px;
            font-size: 18px;
            outline: none;
        }

        input:focus {
            border-color: #172033;
        }

        button {
            margin-top: 14px;
            border: 0;
            border-radius: 8px;
            padding: 13px 20px;
            background: #172033;
            color: white;
            cursor: pointer;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
        }

        th {
            color: #64748b;
            background: #f8fafc;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            background: #e2e8f0;
            font-size: 12px;
        }

        .notice {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        @media (
            max-width: 760px
        ) {

            .grid {
                grid-template-columns: 1fr;
            }

            .header {
                align-items: flex-start;
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <div class="header">

        <div>

            <h1>
                Edge Attendance Terminal
            </h1>

            <div class="subtitle">
                Local attendance capture and offline processing
            </div>

        </div>

        <span class="offline-badge">
            EDGE LOCAL MODE
        </span>

    </div>


    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if ($errors->any())

        <div class="alert alert-error">

            @foreach ($errors->all() as $error)

                <div>
                    {{ $error }}
                </div>

            @endforeach

        </div>

    @endif


    <div class="grid">

        <div class="card">

            <span class="label">
                Edge Device
            </span>

            <div class="value">
                {{ $device?->device_id ?? 'Not Provisioned' }}
            </div>

        </div>

        <div class="card">

            <span class="label">
                Cached Employees
            </span>

            <div class="value">
                {{ $cachedEmployees }}
            </div>

        </div>

        <div class="card">

            <span class="label">
                Awaiting Synchronization
            </span>

            <div class="value">
                {{ $pendingRecords }}
            </div>

        </div>

    </div>


    <div class="panel">

        <div class="panel-header">

            <h2>
                Barcode Attendance
            </h2>

        </div>

        <div class="panel-body">

            @if (!$device)

                <div class="alert alert-error">
                    This terminal has not yet been provisioned.
                    Connect to the central HRMS and refresh the
                    Edge cache first.
                </div>

            @else

                <form
                    method="POST"
                    action="{{ route(
                        'edge-terminal.store'
                    ) }}"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="device_id"
                        value="{{ $device->device_id }}"
                    >

                    <label for="barcode">
                        Scan or Enter Employee Barcode
                    </label>

                    <input
                        type="text"
                        id="barcode"
                        name="barcode"
                        value="{{ old('barcode') }}"
                        placeholder="Scan employee barcode..."
                        autocomplete="off"
                        autofocus
                        required
                    >

                    <button type="submit">
                        Process Attendance
                    </button>

                </form>

            @endif

        </div>

    </div>


    <div class="panel">

        <div class="panel-header">

            <h2>
                Today's Local Attendance
            </h2>

        </div>

        <div class="panel-body">

            @if ($todayRecords->isEmpty())

                <div class="notice">
                    No attendance has been captured
                    locally today.
                </div>

            @else

                <table>

                    <thead>

                    <tr>
                        <th>Employee</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
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
                                {{ $record->check_in
                                    ?->format('H:i:s')
                                    ?? '—' }}
                            </td>

                            <td>
                                {{ $record->check_out
                                    ?->format('H:i:s')
                                    ?? '—' }}
                            </td>

                            <td>
                                <span class="status">
                                    {{ ucfirst(
                                        $record->status
                                    ) }}
                                </span>
                            </td>

                            <td>
                                <span class="status">
                                    {{ ucfirst(
                                        $record
                                            ->sync_status
                                    ) }}
                                </span>
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            @endif

        </div>

    </div>


    <div class="notice">

        Attendance captured on this terminal is stored
        in the local Edge SQLite database. Pending
        records are transferred to the central HRMS
        when synchronization is performed.

    </div>

</div>

</body>

</html>
