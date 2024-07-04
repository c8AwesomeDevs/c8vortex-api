<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checks extends Model
{
    protected $fillable = [
        "transformer_id",
        "check_name",
    ];
    
    public function transformer()
    {
        return $this->belongsTo(Transformer::class);
    }
}
