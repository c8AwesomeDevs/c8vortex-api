<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAsset extends Model
{
    protected $fillable = ['user_id', 'element_id'];
}
