<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $fillable = [
        'element_id',
        'user_name',
        'timestamp',
        'comment', 
    ];

    public function element() {
        return $this->belongsTo(Element::class);
    }
}
