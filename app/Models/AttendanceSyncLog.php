<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_device_id',
        'sync_reference',
        'sync_started_at',
        'sync_completed_at',
        'records_processed',
        'records_synced',
        'records_failed',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sync_started_at' => 'datetime',
        'sync_completed_at' => 'datetime',
        'records_processed' => 'integer',
        'records_synced' => 'integer',
        'records_failed' => 'integer',
    ];

    /**
     * Edge attendance device associated with the synchronization.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(
            AttendanceDevice::class,
            'attendance_device_id'
        );
    }

    /**
     * Determine whether synchronization completed successfully.
     */
    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === 'completed'
            && $this->records_failed === 0;
    }

    /**
     * Determine whether synchronization failed completely.
     */
    public function getHasFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Calculate synchronization success percentage.
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->records_processed === 0) {
            return 0;
        }

        return round(
            ($this->records_synced / $this->records_processed) * 100,
            2
        );
    }
}
