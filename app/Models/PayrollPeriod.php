<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Payroll calculations belonging to this period.
     */
    public function payrollCalculations(): HasMany
    {
        return $this->hasMany(PayrollCalculation::class);
    }

    /**
     * Determine whether the payroll period is open.
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Determine whether the payroll period has been completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }
}
