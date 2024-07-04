<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'subscription_type',
        'subtotal',
        'total',
        'currency',
        'payment_status',
        'activation_date',
        'expiration_date',
        'reference_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_subscription_item_id',
        'activated'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
