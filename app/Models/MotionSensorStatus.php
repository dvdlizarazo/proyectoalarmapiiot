<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotionSensorStatus extends Model
{
    use HasFactory;

    protected $table = 'motion_sensor_status';

    protected $fillable = [
        'sensor_name',
        'status',
        'detected_at',
    ];

}
