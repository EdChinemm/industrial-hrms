<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Attendance Barcode - {{ $employee->full_name }}
    </title>

    <style>

        body {
            margin: 0;
            padding: 40px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fa;
            color: #111827;
        }

        .toolbar {
            max-width: 760px;
            margin: 0 auto 20px;
            display: flex;
            gap: 10px;
        }

        .button {
            display: inline-block;
            border: 0;
            border-radius: 6px;
            padding: 10px 16px;
            background: #172033;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }

        .button-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .barcode-card {
            max-width: 760px;
            margin: auto;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
        }

        .company {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h1 {
            margin: 8px 0 5px;
            font-size: 26px;
        }

        .employee-number {
            color: #6b7280;
            margin-bottom: 28px;
        }

        .barcode {
            margin: 28px auto 12px;
            overflow-x: auto;
        }

        .barcode svg {
            max-width: 100%;
            height: auto;
        }

        .barcode-value {
            font-family: monospace;
            font-size: 16px;
            letter-spacing: 2px;
            margin-top: 12px;
        }

        .details {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            text-align: left;
        }

        .detail {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .detail span {
            display: block;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .detail strong {
            font-size: 14px;
        }

        @media print {

            body {
                background: white;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .barcode-card {
                border: none;
                box-shadow: none;
                max-width: 100%;
            }
        }

    </style>

</head>

<body>

<div class="toolbar">

    <button
        class="button"
        onclick="window.print()"
    >
        Print Barcode
    </button>

    <a
        href="{{ route(
            'employees.show',
            $employee
        ) }}"
        class="button button-secondary"
    >
        Back to Employee
    </a>

</div>


<div class="barcode-card">

    <div class="company">
        Industrial HRMS
    </div>

    <h1>
        {{ $employee->full_name }}
    </h1>

    <div class="employee-number">
        Employee No:
        {{ $employee->employee_number }}
    </div>

    <div class="barcode">
        {!! $barcodeSvg !!}
    </div>

    <div class="barcode-value">
        {{ $employee->activeBarcode->barcode }}
    </div>

    <div class="details">

        <div class="detail">

            <span>Department</span>

            <strong>
                {{ $employee->department->name }}
            </strong>

        </div>

        <div class="detail">

            <span>Position</span>

            <strong>
                {{ $employee->position->title }}
            </strong>

        </div>

        <div class="detail">

            <span>Issued</span>

            <strong>
                {{ $employee->activeBarcode
                    ->issued_at
                    ?->format('d M Y H:i') }}
            </strong>

        </div>

        <div class="detail">

            <span>Status</span>

            <strong>
                Active
            </strong>

        </div>

    </div>

</div>

</body>

</html>
