<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bucket extends Model
{
    protected $fillable = [
        "org_id",
        "influx_bucket_id",
        "influx_bucket_name",
        "influx_host",
        "token_read",
        "token_write",
        "company_id",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
