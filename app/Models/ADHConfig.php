<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ADHConfig extends Model
{
    // use HasFactory;
    protected $fillable = [
        "company_id",
        "name",
        "stream_id",
        "descriptions",
        "type"
    ];
    
    public function transformer()
    {
        return $this->belongsTo(Transformer::class);
    }
}
