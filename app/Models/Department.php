<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'code',
    'description',
    'is_active',
];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Employees belonging to this department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Positions belonging to this department.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}
