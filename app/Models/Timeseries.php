<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeseries extends Model
{
    protected $fillable = [
        "timestamp",
        "tag_id",
        "value",
        "element_id",
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
