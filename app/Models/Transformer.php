<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transformer extends Model
{
    protected $fillable = [
        'element_id',
        'company_id',
        'startup_date',
        'manufacturer',
        'type',
        'construction_year',
        'age_band',
        'line_capacity',
        'winding_voltage',
        'asset_desc',
        'address',
        'country_manufacturer',
        'serial_no',
        'model_no',
        'volt_capacity',
    ];

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
