@extends('layouts.app')

@section('title', 'Employee Profile | Industrial HRMS')

@section('page-heading', 'Employee Profile')

@section(
    'page-description',
    'Employee workforce and attendance information'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">

        <div>
            <h2>
                {{ $employee->full_name }}
            </h2>
        </div>

        <a
            href="{{ route('employees.edit', $employee) }}"
            class="btn btn-primary"
        >
            Edit Employee
        </a>

    </div>

    <div class="section-body">

        <div class="profile-grid">

            <div class="profile-item">
                <span>Employee Number</span>
                <strong>
                    {{ $employee->employee_number }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Department</span>
                <strong>
                    {{ $employee->department->name }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Position</span>
                <strong>
                    {{ $employee->position->title }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Status</span>
                <strong>
                    {{ ucfirst($employee->employment_status) }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Phone</span>
                <strong>
                    {{ $employee->phone ?: '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Email</span>
                <strong>
                    {{ $employee->email ?: '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>National ID</span>
                <strong>
                    {{ $employee->national_id ?: '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Employment Date</span>
                <strong>
                    {{ $employee->employment_date?->format('d M Y') ?? '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Attendance Records</span>
                <strong>
                    {{ $employee->attendanceRecords->count() }}
                </strong>
            </div>

        </div>

    </div>

</div>

<div class="section">

    <div class="section-header">
        <h2>Attendance Summary</h2>
    </div>

    <div class="section-body">

        <p>
            Recorded attendance entries:
            <strong>
                {{ $employee->attendanceRecords->count() }}
            </strong>
        </p>

        <p>
            This employee will later receive a barcode for
            Edge-based attendance capture.
        </p>

    </div>

</div>

<div class="section">

    <div class="section-header">

        <h2>Attendance Barcode</h2>

    </div>

    <div class="section-body">

        @if ($employee->activeBarcode)

            <p>
                Active Barcode:
            </p>

            <div
                style="
                    font-size: 22px;
                    font-weight: bold;
                    letter-spacing: 2px;
                    margin: 15px 0;
                "
            >
                {{ $employee->activeBarcode->barcode }}
            </div>

            <p>
                Issued:
                {{ $employee->activeBarcode->issued_at?->format('d M Y H:i') }}
            </p>

            <div class="form-actions">

                <form
                    method="POST"
                    action="{{ route('employees.barcode.store', $employee) }}"
                >
                    @csrf


                    <a
    href="{{ route(
        'employees.barcode.print',
        $employee
    ) }}"
    class="btn btn-primary"
    target="_blank"
>
    Print Barcode
</a>
                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        Replace Barcode
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('employees.barcode.destroy', $employee) }}"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        Revoke Barcode
                    </button>
                </form>

            </div>

        @else

            <p>
                No active attendance barcode has been assigned.
            </p>

            <form
                method="POST"
                action="{{ route('employees.barcode.store', $employee) }}"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Generate Attendance Barcode
                </button>

            </form>

        @endif

    </div>

</div>

@endsection
