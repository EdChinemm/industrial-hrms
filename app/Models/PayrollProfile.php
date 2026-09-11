<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'pay_frequency',
        'hourly_rate',
        'standard_hours_per_day',
        'standard_working_days',
        'overtime_enabled',
        'overtime_rate_multiplier',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'standard_hours_per_day' => 'decimal:2',
        'standard_working_days' => 'integer',
        'overtime_enabled' => 'boolean',
        'overtime_rate_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Employee who owns this payroll profile.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
