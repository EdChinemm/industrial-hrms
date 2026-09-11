@extends('layouts.app')

@section('title', 'Leave Management | Industrial HRMS')

@section('page-heading', 'Leave Management')

@section(
    'page-description',
    'Employee leave requests and approval workflow'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>Create Leave Request</h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route('leave.store') }}"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>Employee *</label>

                    <select
                        class="form-control"
                        name="employee_id"
                        required
                    >

                        <option value="">
                            Select Employee
                        </option>

                        @foreach ($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    old('employee_id')
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

                    <label>Leave Type *</label>

                    <select
                        class="form-control"
                        name="leave_type_id"
                        required
                    >

                        <option value="">
                            Select Leave Type
                        </option>

                        @foreach ($leaveTypes as $type)

                            <option
                                value="{{ $type->id }}"
                                @selected(
                                    old('leave_type_id')
                                    == $type->id
                                )
                            >
                                {{ $type->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Start Date *</label>

                    <input
                        class="form-control"
                        type="date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>End Date *</label>

                    <input
                        class="form-control"
                        type="date"
                        name="end_date"
                        value="{{ old('end_date') }}"
                        required
                    >

                </div>

                <div class="form-group full">

                    <label>Reason</label>

                    <textarea
                        class="form-control"
                        name="reason"
                    >{{ old('reason') }}</textarea>

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Submit Leave Request
                </button>

            </div>

        </form>

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Leave Requests</h2>
    </div>

    <div class="table-wrap">

        @if ($leaveRequests->isEmpty())

            <div class="empty-state">
                No leave requests have been recorded.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Period</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Reviewed By</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($leaveRequests as $leave)

                    <tr>

                        <td>
                            {{ $leave->employee->full_name }}
                        </td>

                        <td>
                            {{ $leave->leaveType->name }}
                        </td>

                        <td>
                            {{ $leave->start_date
                                ->format('d M Y') }}
                            —
                            {{ $leave->end_date
                                ->format('d M Y') }}
                        </td>

                        <td>
                            {{ $leave->days_requested }}
                        </td>

                        <td>

                            <span
                                class="badge
                                @if($leave->status === 'approved')
                                    badge-success
                                @elseif($leave->status === 'rejected')
                                    badge-danger
                                @else
                                    badge-warning
                                @endif
                                "
                            >
                                {{ ucfirst($leave->status) }}
                            </span>

                        </td>

                        <td>
                            {{ $leave->reviewer?->name ?? '—' }}
                        </td>

                        <td>

                            @if ($leave->status === 'pending')

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'leave.review',
                                        $leave
                                    ) }}"
                                >

                                    @csrf
                                    @method('PUT')

                                    <input
                                        class="form-control"
                                        type="text"
                                        name="review_comment"
                                        placeholder="Optional comment"
                                        style="
                                            min-width: 160px;
                                            margin-bottom: 6px;
                                        "
                                    >

                                    <div class="actions">

                                        <button
                                            type="submit"
                                            name="status"
                                            value="approved"
                                            class="btn btn-primary"
                                        >
                                            Approve
                                        </button>

                                        <button
                                            type="submit"
                                            name="status"
                                            value="rejected"
                                            class="btn btn-secondary"
                                        >
                                            Reject
                                        </button>

                                    </div>

                                </form>

                            @else

                                Reviewed
                                {{ $leave->reviewed_at
                                    ?->format('d M Y H:i') }}

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <div class="section-body">
                {{ $leaveRequests->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
