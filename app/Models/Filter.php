<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $fillable = [
        "key",
        "value",
        "sensor_id",
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
