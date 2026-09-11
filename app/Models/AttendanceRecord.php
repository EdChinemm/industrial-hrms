<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'attendance_source',
        'device_id',
        'sync_status',
        'synced_at',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'synced_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function device()
    {
        return $this->belongsTo(
            AttendanceDevice::class,
            'device_id',
            'device_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function getHoursWorkedAttribute(): float
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        return round(
            $this->check_in->diffInMinutes($this->check_out) / 60,
            2
        );
    }

    public function getIsLateAttribute(): bool
    {
        return $this->status === 'late';
    }

    public function getIsSyncedAttribute(): bool
    {
        return $this->sync_status === 'synced';
    }
}
