<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserElement extends Model
{
    protected $fillable = ['user_id', 'element_id'];
}
