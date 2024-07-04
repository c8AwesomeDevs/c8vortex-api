<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    // use HasFactory;

    protected $fillable = [
        "event_id",
        "start_time",
        "end_time",
        "severity",
        "value"
    ];
    
    // public function transformer()
    // {
    //     return $this->belongsTo(Transformer::class);
    // }
}
