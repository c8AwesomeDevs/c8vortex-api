<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    protected $fillable = ['company_id', 'parent_id', 'name', 'description', 'path', 'has_child'];

    public function parent() {
        return $this->belongsTo(Element::class, 'parent_id', 'id');
    }
    public function children() {
        return $this->hasMany(Element::class, 'parent_id', 'id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function attribute_values() {
        return $this->hasMany(AttributeValue::class);
    }
    public function transformer_details() {
        return $this->hasOne(Transformer::class);
    }

    public function latest_attribute_value() {
        return $this->hasOne(AttributeValue::class)->orderBy('timestamp', 'DESC')->limit(1);
    }
}
