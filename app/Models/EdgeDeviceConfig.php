<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdgeDeviceConfig extends Model
{
    protected $connection = 'edge';

    protected $table = 'edge_device_config';

    protected $fillable = [
        'device_id',
        'device_name',
        'location',
        'is_active',
        'cached_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cached_at' => 'datetime',
    ];
}