<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
    'department_id',
    'title',
    'code',
    'description',
    'is_active',
];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Department this position belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Employees assigned to this position.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
