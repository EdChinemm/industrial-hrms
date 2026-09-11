<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'basic_salary',
        'expected_working_days',
        'days_present',
        'days_absent',
        'hours_worked',
        'overtime_hours',
        'estimated_basic_earnings',
        'estimated_overtime_earnings',
        'estimated_total_earnings',
        'calculation_status',
        'calculated_at',
        'calculation_notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'expected_working_days' => 'integer',
        'days_present' => 'integer',
        'days_absent' => 'integer',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'estimated_basic_earnings' => 'decimal:2',
        'estimated_overtime_earnings' => 'decimal:2',
        'estimated_total_earnings' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    /**
     * Employee associated with the calculation.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Payroll period associated with the calculation.
     */
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    /**
     * Determine whether the calculation has been completed.
     */
    public function getIsCalculatedAttribute(): bool
    {
        return $this->calculation_status === 'calculated';
    }
}
