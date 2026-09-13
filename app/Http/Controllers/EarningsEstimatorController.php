<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\PayrollCalculation;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EarningsEstimatorController extends Controller
{
    /**
     * Display the attendance-based earnings estimator.
     */
    public function index(Request $request): View
    {
        $employees = Employee::with('payrollProfile')
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $selectedEmployee = null;

        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::with('payrollProfile')
                ->find($request->employee_id);
        }

        if (!$selectedEmployee) {
            $selectedEmployee = $employees->first();
        }

        $periods = PayrollPeriod::latest('start_date')
            ->limit(12)
            ->get();

        $calculations = PayrollCalculation::with([
            'employee',
            'payrollPeriod',
        ])
            ->latest('calculated_at')
            ->limit(30)
            ->get();

        $activeProfiles = PayrollProfile::where(
            'is_active',
            true
        )->count();

        return view(
            'earnings.index',
            compact(
                'employees',
                'selectedEmployee',
                'periods',
                'calculations',
                'activeProfiles'
            )
        );
    }

    /**
     * Create or update an employee earnings profile.
     */
    public function storeProfile(
        Request $request,
        Employee $employee
    ): RedirectResponse {
        $validated = $request->validate([
            'basic_salary' => [
                'required',
                'numeric',
                'min:0',
            ],

            'pay_frequency' => [
                'required',
                Rule::in([
                    'monthly',
                    'weekly',
                    'daily',
                    'hourly',
                ]),
            ],

            'hourly_rate' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'standard_hours_per_day' => [
                'required',
                'numeric',
                'min:1',
                'max:24',
            ],

            'standard_working_days' => [
                'required',
                'integer',
                'min:1',
                'max:31',
            ],

            'overtime_enabled' => [
                'required',
                'boolean',
            ],

            'overtime_rate_multiplier' => [
                'required',
                'numeric',
                'min:1',
            ],

            'effective_from' => [
                'nullable',
                'date',
            ],

            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],
        ]);

        PayrollProfile::updateOrCreate(
            [
                'employee_id' => $employee->id,
            ],
            [
                'basic_salary' =>
                    $validated['basic_salary'],

                'pay_frequency' =>
                    $validated['pay_frequency'],

                'hourly_rate' =>
                    $validated['hourly_rate'] ?? null,

                'standard_hours_per_day' =>
                    $validated['standard_hours_per_day'],

                'standard_working_days' =>
                    $validated['standard_working_days'],

                'overtime_enabled' =>
                    $validated['overtime_enabled'],

                'overtime_rate_multiplier' =>
                    $validated['overtime_rate_multiplier'],

                'is_active' => true,

                'effective_from' =>
                    $validated['effective_from'] ?? null,

                'effective_to' =>
                    $validated['effective_to'] ?? null,
            ]
        );

        return redirect()
            ->route(
                'earnings.index',
                ['employee_id' => $employee->id]
            )
            ->with(
                'success',
                'Earnings profile saved successfully.'
            );
    }

    /**
     * Create an earnings calculation period.
     */
    public function storePeriod(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
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

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        PayrollPeriod::updateOrCreate(
            [
                'start_date' =>
                    $validated['start_date'],

                'end_date' =>
                    $validated['end_date'],
            ],
            [
                'name' =>
                    $validated['name'],

                'status' =>
                    'open',

                'description' =>
                    $validated['description'] ?? null,
            ]
        );

        return back()->with(
            'success',
            'Earnings period created successfully.'
        );
    }

    /**
     * Calculate estimated earnings for all employees
     * with active earnings profiles.
     */
    public function calculate(
        PayrollPeriod $payrollPeriod
    ): RedirectResponse {
        $profiles = PayrollProfile::with('employee')
            ->where('is_active', true)
            ->get();

        if ($profiles->isEmpty()) {
            return back()->withErrors([
                'earnings' =>
                    'No active employee earnings profiles are available.',
            ]);
        }

        $payrollPeriod->update([
            'status' => 'processing',
        ]);

        $calendarWorkingDays = collect(
            CarbonPeriod::create(
                $payrollPeriod->start_date,
                $payrollPeriod->end_date
            )
        )
            ->filter(
                fn ($date) => $date->isWeekday()
            )
            ->count();

        foreach ($profiles as $profile) {
            $employee = $profile->employee;

            if (!$employee) {
                continue;
            }

            $records = AttendanceRecord::where(
                'employee_id',
                $employee->id
            )
                ->whereBetween(
                    'attendance_date',
                    [
                        $payrollPeriod
                            ->start_date
                            ->toDateString(),

                        $payrollPeriod
                            ->end_date
                            ->toDateString(),
                    ]
                )
                ->get();

            $fullPresentDays = $records
                ->whereIn(
                    'status',
                    [
                        'present',
                        'late',
                    ]
                )
                ->count();

            $halfDays = $records
                ->where(
                    'status',
                    'half_day'
                )
                ->count();

            $attendanceUnits =
                $fullPresentDays +
                ($halfDays * 0.5);

            $daysPresent =
                $fullPresentDays +
                $halfDays;

            $expectedWorkingDays =
                $calendarWorkingDays > 0
                    ? $calendarWorkingDays
                    : (int) $profile
                        ->standard_working_days;

            $daysAbsent = max(
                $expectedWorkingDays -
                $daysPresent,
                0
            );

            $hoursWorked = round(
                $records->sum(
                    fn ($record) =>
                        $record->hours_worked
                ),
                2
            );

            $standardHours =
                (float) $profile
                    ->standard_hours_per_day;

            $overtimeHours = 0;

            if ($profile->overtime_enabled) {
                $overtimeHours = round(
                    $records->sum(
                        function ($record) use (
                            $standardHours
                        ) {
                            return max(
                                $record->hours_worked -
                                $standardHours,
                                0
                            );
                        }
                    ),
                    2
                );
            }

            $basicSalary =
                (float) $profile->basic_salary;

            $standardWorkingDays = max(
                (int) $profile
                    ->standard_working_days,
                1
            );

            $hourlyRate =
                (float) (
                    $profile->hourly_rate ?? 0
                );

            /*
             * If no explicit hourly rate is supplied,
             * derive one from the basic salary.
             */
            if ($hourlyRate <= 0) {
                $hourlyRate =
                    $basicSalary /
                    (
                        $standardWorkingDays *
                        max($standardHours, 1)
                    );
            }

            /*
             * Hourly employees are estimated directly
             * from recorded hours.
             *
             * Other frequencies use attendance-day
             * prorating against the employee profile.
             */
            if (
                $profile->pay_frequency === 'hourly'
            ) {
                $estimatedBasic =
                    $hoursWorked * $hourlyRate;
            } else {
                $dailyRate =
                    $basicSalary /
                    $standardWorkingDays;

                $estimatedBasic =
                    $dailyRate *
                    $attendanceUnits;
            }

            $estimatedOvertime = 0;

            if ($profile->overtime_enabled) {
                $estimatedOvertime =
                    $overtimeHours *
                    $hourlyRate *
                    (float) $profile
                        ->overtime_rate_multiplier;
            }

            $estimatedTotal =
                $estimatedBasic +
                $estimatedOvertime;

            PayrollCalculation::updateOrCreate(
                [
                    'payroll_period_id' =>
                        $payrollPeriod->id,

                    'employee_id' =>
                        $employee->id,
                ],
                [
                    'basic_salary' =>
                        $basicSalary,

                    'expected_working_days' =>
                        $expectedWorkingDays,

                    'days_present' =>
                        $daysPresent,

                    'days_absent' =>
                        $daysAbsent,

                    'hours_worked' =>
                        $hoursWorked,

                    'overtime_hours' =>
                        $overtimeHours,

                    'estimated_basic_earnings' =>
                        round(
                            $estimatedBasic,
                            2
                        ),

                    'estimated_overtime_earnings' =>
                        round(
                            $estimatedOvertime,
                            2
                        ),

                    'estimated_total_earnings' =>
                        round(
                            $estimatedTotal,
                            2
                        ),

                    'calculation_status' =>
                        'calculated',

                    'calculated_at' =>
                        now(),

                    'calculation_notes' =>
                        'Attendance-based estimate generated from synchronized attendance records.',
                ]
            );
        }

        $payrollPeriod->update([
            'status' => 'completed',
        ]);

        return back()->with(
            'success',
            'Attendance-based earnings calculations completed successfully.'
        );
    }
}
