<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'position_id',
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'national_id',
        'phone',
        'email',
        'address',
        'employment_date',
        'termination_date',
        'employment_status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'employment_date' => 'date',
        'termination_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(
                implode(' ', array_filter([
                    $this->first_name,
                    $this->middle_name,
                    $this->last_name,
                ]))
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Organizational Relationships
    |--------------------------------------------------------------------------
    */

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Employee Records
    |--------------------------------------------------------------------------
    */

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Relationships
    |--------------------------------------------------------------------------
    */

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(EmployeeBarcode::class);
    }

    public function activeBarcode(): HasOne
    {
        return $this->hasOne(EmployeeBarcode::class)
            ->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Payroll Estimator Relationships
    |--------------------------------------------------------------------------
    */

    public function payrollProfile(): HasOne
    {
        return $this->hasOne(PayrollProfile::class);
    }

    public function payrollCalculations(): HasMany
    {
        return $this->hasMany(PayrollCalculation::class);
    }
}
