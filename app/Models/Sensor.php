<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = [
        "transformer_id",
        "bucket_id",
        "sensor_type_id",
        "_measurement",
    ];

    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }

    public function transformer()
    {
        return $this->belongsTo(Transformer::class);
    }

    public function sensor_type()
    {
        return $this->belongsTo(SensorType::class);
    }
}
