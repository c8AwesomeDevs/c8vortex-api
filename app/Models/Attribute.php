<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['element_id','name', 'description', 'stream_name', 'path'];
}
