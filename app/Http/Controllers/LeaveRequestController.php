<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $leaveRequests = LeaveRequest::with([
            'employee.department',
            'leaveType',
            'reviewer',
        ])
            ->latest()
            ->paginate(15);

        $employees = Employee::where(
            'employment_status',
            'active'
        )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $leaveTypes = LeaveType::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'leave.index',
            compact(
                'leaveRequests',
                'employees',
                'leaveTypes'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],

            'leave_type_id' => [
                'required',
                'integer',
                'exists:leave_types,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /*
         * Prevent overlapping pending/approved leave.
         */
        $overlapExists = LeaveRequest::where(
            'employee_id',
            $validated['employee_id']
        )
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->where(function ($query) use ($validated) {

                $query
                    ->whereDate(
                        'start_date',
                        '<=',
                        $validated['end_date']
                    )
                    ->whereDate(
                        'end_date',
                        '>=',
                        $validated['start_date']
                    );
            })
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'start_date' =>
                    'This employee already has an overlapping pending or approved leave request.',
            ]);
        }

        $start = Carbon::parse(
            $validated['start_date']
        );

        $end = Carbon::parse(
            $validated['end_date']
        );

        $daysRequested =
            $start->diffInDays($end) + 1;

        LeaveRequest::create([
            'employee_id' =>
                $validated['employee_id'],

            'leave_type_id' =>
                $validated['leave_type_id'],

            'start_date' =>
                $validated['start_date'],

            'end_date' =>
                $validated['end_date'],

            'days_requested' =>
                $daysRequested,

            'status' =>
                'pending',

            'reason' =>
                $validated['reason'] ?? null,
        ]);

        return back()->with(
            'success',
            'Leave request created successfully.'
        );
    }

    public function review(
        Request $request,
        LeaveRequest $leaveRequest
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'approved',
                    'rejected',
                ]),
            ],

            'review_comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors([
                'leave' =>
                    'Only pending leave requests can be reviewed.',
            ]);
        }

        $leaveRequest->update([
            'status' =>
                $validated['status'],

            'review_comment' =>
                $validated['review_comment'] ?? null,

            'reviewed_by' =>
                auth()->id(),

            'reviewed_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Leave request ' .
            $validated['status'] .
            ' successfully.'
        );
    }
}
