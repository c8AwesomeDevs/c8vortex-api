<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Timeseries;
use Illuminate\Http\Request;

class TimeseriesController extends Controller
{
    public function store(Request $request)
    {
        foreach ($request->values as $row) {
            $data = [
                "timestamp" => $row['timestamp'],
                "tag_id" => $row['field'],
                "value" => $row['value'],
                "element_id" => $request->element_ID
            ];

            Timeseries::create($data);
        }

        return $request;
    }
}
