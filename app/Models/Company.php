<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_id', 'company_name', 'domain', 'country', 'industry', 'hear_aboutus', 'max_root', 'max_sub', 'max_tfmr', 'max_datapoints'
    ];

    public function elements()
    {
        return $this->hasMany('App\Element');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Models\Subscription');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
