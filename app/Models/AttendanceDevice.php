<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'device_name',
        'location',
        'device_type',
        'ip_address',
        'mac_address',
        'is_active',
        'last_sync_at',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    /**
     * Attendance records captured by this device.
     *
     * attendance_records.device_id references
     * attendance_devices.device_id.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(
            AttendanceRecord::class,
            'device_id',
            'device_id'
        );
    }

    /**
     * Synchronization logs belonging to this device.
     *
     * attendance_sync_logs.attendance_device_id references
     * attendance_devices.id.
     */
    public function syncLogs(): HasMany
    {
        return $this->hasMany(
            AttendanceSyncLog::class,
            'attendance_device_id'
        );
    }
}
