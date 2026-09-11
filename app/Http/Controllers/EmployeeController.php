<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display employee records.
     */
    public function index(): View
    {
        $employees = Employee::with([
            'department',
            'position',
        ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the employee registration form.
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::where('is_active', true)
            ->with('department')
            ->orderBy('title')
            ->get();

        return view(
            'employees.create',
            compact('departments', 'positions')
        );
    }

    /**
     * Store a new employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEmployee($request);

        $this->validatePositionDepartment(
            $validated['position_id'],
            $validated['department_id']
        );

        $employee = Employee::create($validated);

        return redirect()
            ->route('employees.show', $employee)
            ->with(
                'success',
                'Employee registered successfully.'
            );
    }

    /**
     * Display an employee profile.
     */
    public function show(Employee $employee): View
{
    $employee->load([
        'department',
        'position',
        'documents',
        'attendanceRecords',
        'leaveRequests',
        'payrollProfile',
        'activeBarcode',
        'barcodes',
    ]);

    return view(
        'employees.show',
        compact('employee')
    );
}

    /**
     * Show the employee edit form.
     */
    public function edit(Employee $employee): View
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::where('is_active', true)
            ->with('department')
            ->orderBy('title')
            ->get();

        return view(
            'employees.edit',
            compact(
                'employee',
                'departments',
                'positions'
            )
        );
    }

    /**
     * Update an employee record.
     */
    public function update(
        Request $request,
        Employee $employee
    ): RedirectResponse {

        $validated = $this->validateEmployee(
            $request,
            $employee
        );

        $this->validatePositionDepartment(
            $validated['position_id'],
            $validated['department_id']
        );

        $employee->update($validated);

        return redirect()
            ->route('employees.show', $employee)
            ->with(
                'success',
                'Employee information updated successfully.'
            );
    }

    /**
     * Validate employee information.
     */
    private function validateEmployee(
        Request $request,
        ?Employee $employee = null
    ): array {

        return $request->validate([
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'position_id' => [
                'required',
                'integer',
                'exists:positions,id',
            ],

            'employee_number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('employees', 'employee_number')
                    ->ignore($employee?->id),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'gender' => [
                'nullable',
                'string',
                'max:20',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            'national_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('employees', 'national_id')
                    ->ignore($employee?->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('employees', 'email')
                    ->ignore($employee?->id),
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'employment_date' => [
                'nullable',
                'date',
            ],

            'termination_date' => [
                'nullable',
                'date',
                'after_or_equal:employment_date',
            ],

            'employment_status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'suspended',
                    'terminated',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /**
     * Ensure the selected position belongs to the selected department.
     */
    private function validatePositionDepartment(
        int $positionId,
        int $departmentId
    ): void {

        $valid = Position::whereKey($positionId)
            ->where('department_id', $departmentId)
            ->where('is_active', true)
            ->exists();

        if (!$valid) {
            throw ValidationException::withMessages([
                'position_id' =>
                    'The selected position does not belong to the selected department.',
            ]);
        }
    }
}
