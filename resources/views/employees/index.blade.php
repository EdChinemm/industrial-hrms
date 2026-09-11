@extends('layouts.app')

@section('title', 'Employees | Industrial HRMS')

@section('page-heading', 'Employees')

@section(
    'page-description',
    'Employee registration and workforce records'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">

        <h2>
            Employee Records
        </h2>

        <a
            href="{{ route('employees.create') }}"
            class="btn btn-primary"
        >
            Register Employee
        </a>

    </div>

    <div class="table-wrap">

        @if ($employees->isEmpty())

            <div class="empty-state">
                No employees have been registered yet.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Employee No.</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($employees as $employee)

                    <tr>

                        <td>
                            {{ $employee->employee_number }}
                        </td>

                        <td>
                            {{ $employee->full_name }}
                        </td>

                        <td>
                            {{ $employee->department->name }}
                        </td>

                        <td>
                            {{ $employee->position->title }}
                        </td>

                        <td>

                            <span
                                class="badge
                                {{ $employee->employment_status === 'active'
                                    ? 'badge-success'
                                    : 'badge-warning' }}"
                            >
                                {{ ucfirst($employee->employment_status) }}
                            </span>

                        </td>

                        <td>

                            <div class="actions">

                                <a
                                    href="{{ route('employees.show', $employee) }}"
                                    class="btn btn-secondary"
                                >
                                    View
                                </a>

                                <a
                                    href="{{ route('employees.edit', $employee) }}"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <div class="section-body">
                {{ $employees->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
