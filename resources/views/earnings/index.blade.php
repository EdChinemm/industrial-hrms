@extends('layouts.app')

@section(
    'title',
    'Earnings Estimator | Industrial HRMS'
)

@section(
    'page-heading',
    'Attendance-Based Earnings Estimator'
)

@section(
    'page-description',
    'Estimate employee earnings from synchronized attendance data'
)

@section('content')

<div class="grid">

    <div class="card">
        <span class="stat-label">
            Active Employees
        </span>

        <strong class="stat-value">
            {{ $employees->count() }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">
            Earnings Profiles
        </span>

        <strong class="stat-value">
            {{ $activeProfiles }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">
            Earnings Periods
        </span>

        <strong class="stat-value">
            {{ $periods->count() }}
        </strong>
    </div>

    <div class="card">
        <span class="stat-label">
            Calculations
        </span>

        <strong class="stat-value">
            {{ $calculations->count() }}
        </strong>
    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Employee Earnings Profile</h2>
    </div>

    <div class="section-body">

        <form
            method="GET"
            action="{{ route('earnings.index') }}"
        >

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        Employee
                    </label>

                    <select
                        class="form-control"
                        name="employee_id"
                        required
                    >

                        @foreach ($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    $selectedEmployee
                                    && $selectedEmployee->id
                                    === $employee->id
                                )
                            >
                                {{ $employee->employee_number }}
                                —
                                {{ $employee->full_name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-secondary"
                >
                    Load Employee
                </button>

            </div>

        </form>


        @if ($selectedEmployee)

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid #e5e7eb;">

            <form
                method="POST"
                action="{{ route(
                    'earnings.profile.store',
                    $selectedEmployee
                ) }}"
            >

                @csrf

                @php
                    $profile =
                        $selectedEmployee
                            ->payrollProfile;
                @endphp

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Basic Salary / Reference Earnings (K) *
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            step="0.01"
                            min="0"
                            name="basic_salary"
                            value="{{ old(
                                'basic_salary',
                                $profile?->basic_salary ?? 0
                            ) }}"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>
                            Pay Frequency *
                        </label>

                        <select
                            class="form-control"
                            name="pay_frequency"
                            required
                        >

                            @foreach ([
                                'monthly',
                                'weekly',
                                'daily',
                                'hourly'
                            ] as $frequency)

                                <option
                                    value="{{ $frequency }}"
                                    @selected(
                                        old(
                                            'pay_frequency',
                                            $profile
                                                ?->pay_frequency
                                                ?? 'monthly'
                                        )
                                        === $frequency
                                    )
                                >
                                    {{ ucfirst(
                                        $frequency
                                    ) }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group">

                        <label>
                            Hourly Rate (K)
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            step="0.01"
                            min="0"
                            name="hourly_rate"
                            value="{{ old(
                                'hourly_rate',
                                $profile?->hourly_rate
                            ) }}"
                        >

                    </div>

                    <div class="form-group">

                        <label>
                            Standard Hours Per Day *
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            step="0.25"
                            min="1"
                            max="24"
                            name="standard_hours_per_day"
                            value="{{ old(
                                'standard_hours_per_day',
                                $profile
                                    ?->standard_hours_per_day
                                    ?? 8
                            ) }}"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>
                            Standard Working Days *
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            min="1"
                            max="31"
                            name="standard_working_days"
                            value="{{ old(
                                'standard_working_days',
                                $profile
                                    ?->standard_working_days
                                    ?? 22
                            ) }}"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>
                            Overtime Multiplier *
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            step="0.01"
                            min="1"
                            name="overtime_rate_multiplier"
                            value="{{ old(
                                'overtime_rate_multiplier',
                                $profile
                                    ?->overtime_rate_multiplier
                                    ?? 1.50
                            ) }}"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label>
                            Effective From
                        </label>

                        <input
                            class="form-control"
                            type="date"
                            name="effective_from"
                            value="{{ old(
                                'effective_from',
                                $profile?->effective_from
                                    ?->format('Y-m-d')
                            ) }}"
                        >

                    </div>

                    <div class="form-group">

                        <label>
                            Effective To
                        </label>

                        <input
                            class="form-control"
                            type="date"
                            name="effective_to"
                            value="{{ old(
                                'effective_to',
                                $profile?->effective_to
                                    ?->format('Y-m-d')
                            ) }}"
                        >

                    </div>

                    <div class="form-group full">

                        <label>
                            Overtime
                        </label>

                        <input
                            type="hidden"
                            name="overtime_enabled"
                            value="0"
                        >

                        <label style="font-weight: normal;">

                            <input
                                type="checkbox"
                                name="overtime_enabled"
                                value="1"
                                @checked(
                                    old(
                                        'overtime_enabled',
                                        $profile
                                            ?->overtime_enabled
                                            ?? false
                                    )
                                )
                            >

                            Enable overtime calculation

                        </label>

                    </div>

                </div>

                <div class="form-actions">

                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        Save Earnings Profile
                    </button>

                </div>

            </form>

        @endif

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Create Earnings Period</h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route(
                'earnings.period.store'
            ) }}"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        Period Name *
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        name="name"
                        placeholder="e.g. September 2026"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Start Date *
                    </label>

                    <input
                        class="form-control"
                        type="date"
                        name="start_date"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        End Date *
                    </label>

                    <input
                        class="form-control"
                        type="date"
                        name="end_date"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Description
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        name="description"
                    >

                </div>

            </div>

            <div class="form-actions">

                <button
                    class="btn btn-primary"
                    type="submit"
                >
                    Create Period
                </button>

            </div>

        </form>

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Earnings Periods</h2>
    </div>

    <div class="table-wrap">

        @if ($periods->isEmpty())

            <div class="empty-state">
                No earnings periods have been created.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Period</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($periods as $period)

                    <tr>

                        <td>
                            {{ $period->name }}
                        </td>

                        <td>
                            {{ $period->start_date
                                ->format('d M Y') }}
                            —
                            {{ $period->end_date
                                ->format('d M Y') }}
                        </td>

                        <td>
                            <span class="badge">
                                {{ ucfirst(
                                    $period->status
                                ) }}
                            </span>
                        </td>

                        <td>

                            <form
                                method="POST"
                                action="{{ route(
                                    'earnings.calculate',
                                    $period
                                ) }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Calculate
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
        <h2>Estimated Earnings Results</h2>
    </div>

    <div class="table-wrap">

        @if ($calculations->isEmpty())

            <div class="empty-state">
                No earnings calculations have been generated.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Period</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Hours</th>
                    <th>Overtime</th>
                    <th>Basic Estimate</th>
                    <th>OT Estimate</th>
                    <th>Total Estimate</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($calculations as $calculation)

                    <tr>

                        <td>
                            {{ $calculation
                                ->employee
                                ->full_name }}
                        </td>

                        <td>
                            {{ $calculation
                                ->payrollPeriod
                                ->name }}
                        </td>

                        <td>
                            {{ $calculation
                                ->days_present }}
                        </td>

                        <td>
                            {{ $calculation
                                ->days_absent }}
                        </td>

                        <td>
                            {{ number_format(
                                $calculation
                                    ->hours_worked,
                                2
                            ) }}
                        </td>

                        <td>
                            {{ number_format(
                                $calculation
                                    ->overtime_hours,
                                2
                            ) }}
                        </td>

                        <td>
                            K{{ number_format(
                                $calculation
                                    ->estimated_basic_earnings,
                                2
                            ) }}
                        </td>

                        <td>
                            K{{ number_format(
                                $calculation
                                    ->estimated_overtime_earnings,
                                2
                            ) }}
                        </td>

                        <td>
                            <strong>
                                K{{ number_format(
                                    $calculation
                                        ->estimated_total_earnings,
                                    2
                                ) }}
                            </strong>
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>

<div class="section">

    <div class="section-body">

        <small>
            Note: This module provides attendance-based earnings
            estimates for demonstration and workforce evaluation.
            It is not a statutory payroll processing system.
        </small>

    </div>

</div>

@endsection
