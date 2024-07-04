<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = [
        'element_id',
        'company_id',
        'timestamp',
        'acetylene',
        'acetylene_roc',
        'ethylene',
        'ethylene_roc',
        'methane',
        'methane_roc',
        'ethane',
        'ethane_roc',
        'hydrogen',
        'hydrogen_roc',
        'oxygen',
        'carbon_monoxide',
        'carbon_dioxide',
        'tdcg',
        't1',
        't2',
        't3_biotemp',
        't3_fr',
        't3_midel',
        't3_silicon',
        't4',
        't5',
        't6',
        't7',
        'p1',
        'p2',
        'iec_ratio',
        'carbon_ratio',
        'dornenberg',
        'rogers_ratio',
        'nei'
    ];

    public function element() {
        return $this->belongsTo(Element::class);
    }
}
