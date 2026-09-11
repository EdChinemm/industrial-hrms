<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'file_path',
        'issue_date',
        'expiry_date',
        'description',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Employee who owns the document.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Determine whether the document has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date !== null
            && $this->expiry_date->isPast();
    }

    /**
     * Determine whether the document is approaching expiry.
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        if ($this->expiry_date === null) {
            return false;
        }

        return $this->expiry_date->isFuture()
            && $this->expiry_date->diffInDays(now()) <= 30;
    }
}
