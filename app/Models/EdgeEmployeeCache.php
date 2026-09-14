<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdgeEmployeeCache extends Model
{
    protected $connection = 'edge';

    protected $table = 'edge_employee_cache';

    protected $fillable = [
        'employee_id',
        'employee_number',
        'employee_name',
        'barcode',
        'is_active',
        'cached_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cached_at' => 'datetime',
    ];
}