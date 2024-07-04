<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        "tag_name",
        "sensor_id",
        "_field",
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
