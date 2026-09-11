<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdgeAttendanceQueue extends Model
{
    protected $connection = 'edge';

    protected $table = 'edge_attendance_queue';

    protected $fillable = [
        'local_reference',
        'employee_id',
        'employee_number',
        'employee_name',
        'barcode',
        'device_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'sync_status',
        'synced_at',
        'sync_error',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function getIsPendingAttribute(): bool
    {
        return $this->sync_status === 'pending';
    }
}
