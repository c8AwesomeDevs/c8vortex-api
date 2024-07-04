<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Bucket;
use App\Models\Filter;
use App\Models\Sensor;
use App\Models\SensorType;
use App\Models\Tag;
use App\Models\Events;
use App\Models\Transformer;
use App\Models\Check;
use App\Services\InfluxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DateTime;
use DateTimeZone;


class InfluxController extends Controller
{
    public function getCompanyBuckets(Request $request)
    {
        $buckets = Bucket::where('company_id', $request->company_id)
            ->select('id', 'influx_bucket_name')->get();
        return $buckets;
    }

    public function getSensorTypes()
    {
        $sType = SensorType::select('id', 'type')->get();
        return $sType;
    }

    public function testMapping(Request $request)
    {
        $validated = $request->validate([
            "bucket" => "required|integer",
            "sensorType" => "required|integer",
            "_measurement" => "required|string",
            "filters" => "required",
            "tags" => "required",
        ]);

        $bucket = Bucket::where('id', $validated['bucket'])->get();

        $select = [];
        foreach ($validated['tags'] as $key => $value) {
            array_push($select, $value['_field']);
        }
        $str_select = json_encode($select);

        $query = "fields = {$str_select}
        from(bucket: \"{$bucket[0]['influx_bucket_name']}\")
        |>range(start: -15m, stop: now())
        |>filter(fn: (r)=>r[\"_measurement\"] == \"{$validated['_measurement']}\")
        |> filter(fn: (r) => contains(value: r._field, set: fields))";
        // |>filter(fn: (r)=>r['_field'] == 'Acetylene')";

        foreach ($validated['filters'] as $key => $value) {
            $query .= "|>filter(fn: (r)=>r[\"{$value["key"]}\"] == \"{$value["value"]}\") ";
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/vnd.flux',
            'Authorization' => "Token {$bucket[0]['token_read']}"
        ])
            ->withBody($query, 'application/vnd.flux')
            ->post("{$bucket[0]['influx_host']}/api/v2/query?orgID={$bucket[0]['org_id']}");

        if ($response->clientError()) return response()->json(['message' => "Client error: Query sent might be incorrect", 'status_code' => $response->status()], 400);
        if ($response->serverError()) return response()->json(['message' => "Server error: InfluxDB server may be unreachable", 'status_code' => $response->status()], 500);

        // since influx alwaya returns csv we have to parse it to json
        // $csv = ",result,table,_start,_stop,_time,_value,_field,_measurement,asset,brand,customer,model,rating
        // ,_result,0,2023-09-25T01:31:42.060712949Z,2023-09-25T01:32:42.060712949Z,2023-09-25T01:32:00.004Z,67.53350931914824,Hydrogen,multigasSensor,Transformer,GE,Customer X,MS3000,110 kV";
        $csvs = array_map("str_getcsv", explode("\n", trim($response)));
        $datas = [];
        $vars_of_interest = ["_time", "_field", "_value"];
        foreach ($csvs as $key => $csv) {
            if ($key == 0) continue; // skip the first row; that is the headers
            foreach ($csvs[0] as $column_key => $column_name) {
                if (in_array($column_name, $vars_of_interest)) $datas[$key - 1][$column_name] = $csv[$column_key];
            }
        }
        $json = json_encode($datas);

        if ($response->status() == 200) {
            return response()->json(['message' => "Connection Established", 'test_output' => $json], 200);
        } else {
            return response()->json(['message' => "Error Encountered", 'test_output' => $json], 404);
        }
    }

    public function saveMapping(Request $request)
    {
        // first get the transformer where the new Sensor will be attached to
        $transformer = Transformer::where("element_id", intval($request->element))->get()->first();

        // collate all the data needed for the new sensor
        $newSensor = [
            "transformer_id" => $transformer->id,
            "bucket_id" => $request->bucket,
            "sensor_type_id" => $request->sensorType,
            "_measurement" => $request->_measurement
        ];

        $affected = 0;

        // create sensor
        $sensor = Sensor::create($newSensor);
        $affected += 1;


        foreach ($request->filters as $key => $value) {
            // collate all the data needed for one filter of the new sensor
            $data = [
                "key" => $value['key'],
                "value" => $value['value'],
                "sensor_id" => $sensor->id,
            ];

            // create each filter one by one
            Filter::create($data);
            $affected += 1;
        }

        foreach ($request->tags as $key => $value) {
            // collate all the data needed for one tag of the new sensor
            $data = [
                "tag_name" => $value['tag_name'],
                "sensor_id" => $sensor->id,
                "_field" => $value['_field'],
            ];

            // create each tags one by one
            Tag::create($data);
            $affected += 1;
        }

        if ($affected > 0) {
            return response()->json(['message' => "Mapping Created", "records_affected" => $affected], 200);
        } else {
            return response()->json(['message' => "Error Creating Mapping", "records_affected" => $affected], 500);
        }
    }

    public function getTimeseries(Request $request, InfluxService $influxService)
    {
        $validated = $request->validate([
            // 'company' => 'required|string',
            'element_id' => 'required|string',
            'sensor_type' => 'required|string',
            'start' => 'required|string',
            'stop' => 'required|string'
        ]);

        

        // relating element to transformer; only getting the ID of the first
        $transformer = Transformer::where("element_id", $validated['element_id'])->get()->first();
        $validated['transformer'] = $transformer->id;

      

        $queryParts = Sensor::join('buckets', "bucket_id", "=", "buckets.id")->join('sensor_types', 'sensor_type_id', '=', 'sensor_types.id')
            ->where("transformer_id", $validated['transformer'])
            ->where("buckets.company_id", $request->company_id)
            ->where("sensor_types.type", $validated['sensor_type'])
            ->select('sensors.id', 'sensors._measurement', 'buckets.org_id', 'buckets.influx_bucket_name', 'buckets.influx_host', 'buckets.token_read', 'sensor_types.type')
            ->get();

        foreach ($queryParts as $key => $value) {
            $filters =  Sensor::join('filters', 'sensors.id', '=', 'filters.sensor_id')
                ->select('key', 'value')
                ->where('sensors.id', $value['id'])
                ->get();

            $tags = Sensor::join('tags', 'sensors.id', '=', 'tags.sensor_id')
                ->select('tags.id', '_field', 'tag_name')
                ->where('sensors.id', $value['id'])
                ->get();

            $queryParts[$key]['filters'] = $filters;
            $queryParts[$key]['tags'] = $tags;
        }
        // cut if no sensor was found in SQL
        if (count($queryParts) == 0) return response()->json(['message' => "Sensor with provided identifiers was not found"], 404);

        // build the query for influxDB
        // NOTE: when query-ing you always have to use double-qoutes
        // NOTE: "range-start" is inclusive and "range-stop" is exclusive
        // NOTE: influxdb api 2.x.x does not return json, always csv format

        $select = [];
        foreach ($queryParts[0]['tags'] as $key => $value) {
            array_push($select, $value['_field']);
        }
        $str_select = json_encode($select);

        $query = "fields = {$str_select}
        from(bucket: \"{$queryParts[0]['influx_bucket_name']}\")
        |>range(start: {$validated['start']}, stop: {$validated['stop']})
        |>filter(fn: (r)=>r[\"_measurement\"] == \"{$queryParts[0]['_measurement']}\")
        |> filter(fn: (r) => contains(value: r._field, set: fields))";
        // |>filter(fn: (r)=>r['_field'] == 'Acetylene')";

        foreach ($queryParts[0]['filters'] as $key => $value) {
            $query .= "|>filter(fn: (r)=>r[\"{$value["key"]}\"] == \"{$value["value"]}\") ";
        }

        // send request to influx
        $response = Http::withHeaders([
            'Content-Type' => 'application/vnd.flux',
            'Authorization' => "Token {$queryParts[0]['token_read']}"
        ])
            ->withBody($query, 'application/vnd.flux')
            ->post("{$queryParts[0]['influx_host']}/api/v2/query?orgID={$queryParts[0]['org_id']}");

        if ($response->clientError()) return response()->json(['message' => "Client error: Query sent might be incorrect", 'status_code' => $response->status()], 400);
        if ($response->serverError()) return response()->json(['message' => "Server error: InfluxDB server may be unreachable", 'status_code' => $response->status()], 500);

        // since influx alwaya returns csv we have to parse it to json
        // $csv = ",result,table,_start,_stop,_time,_value,_field,_measurement,asset,brand,customer,model,rating
        // ,_result,0,2023-09-25T01:31:42.060712949Z,2023-09-25T01:32:42.060712949Z,2023-09-25T01:32:00.004Z,67.53350931914824,Hydrogen,multigasSensor,Transformer,GE,Customer X,MS3000,110 kV";
        $csvs = array_map("str_getcsv", explode("\n", trim($response)));
        $datas = [];
        $vars_of_interest = ["_time", "_field", "_value"];
        foreach ($csvs as $key => $csv) {
            if ($key == 0) continue; // skip the first row; that is the headers
            foreach ($csvs[0] as $column_key => $column_name) {
                if (in_array($column_name, $vars_of_interest)) $datas[$key - 1][$column_name] = $csv[$column_key];
            }
        }

        $start_time = $influxService->convertToLocalizedDateTime($validated['start']);
        $end_time = $influxService->convertToLocalizedDateTime($validated['stop']);
        
        $check_name = $influxService->getCheckname($transformer->id);
        // return response()->json(['check_name' => $check_name[0]->check_name]);
        $events_data = $influxService->getEvents($check_name[0]->check_name, $start_time, $end_time);
       

        $results = $influxService->clean_results($tags, $datas);
        if ($results["clean_status"] == false) return $response->json(["message" => $results["message"]], 500);

        return response()->json(["results_clean" => $results['clean_data'],
                                    "events" => $events_data], 200);
    }

    public function saveBucket(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required',
            'org_id' => 'required|string',
            'influx_bucket_id' => 'required|string',
            'influx_bucket_name' => 'required|string',
            'influx_host' => 'required|string',
            'token_read' => 'nullable',
            'token_write' => 'nullable',
        ]);

        $bucket = Bucket::create($validated);

        return $bucket;
    }

    public function saveSensorType(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
        ]);

        $sensorType = SensorType::create($validated);

        return $sensorType;
    }

    

    public function saveEvents(Request $request, InfluxService $influxService)
    {
        $dt = new DateTime($request->_time, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Asia/Manila'));
        $formattedDateTime = $dt->format('Y-m-d H:i:s');

        $current_event = $influxService->getcurrentEvent($request->_check_name);
       
        $level = $influxService->changeLevel($request->_level);
       
        if ($current_event && $current_event->severity != $level) {
            // Update the end_time of the latest event
            $current_event->end_time = $formattedDateTime;
            $current_event->save();
        }else if ($current_event && $current_event->severity == $level){
            $current_event->delete();
        }   


        // Create the new event
        $events_dt = [
            'event_id' => $request->_check_name,
            'severity' => $level,
            'start_time' => $formattedDateTime,
            'end_time' => null,
            'value' => $request->Hydrogen
        ];

        $response = $influxService->saveEvents($events_dt);

        return response()->json(['message' => "Event saved successfully"], 200);
    }

    public function createCheck(){
                
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer pByeCOnFddNXNNpjXeW-yKHER72jooR0w3b3iPad-AC9B75uAwW6YWoYXHwzsigQjVJmLiKPdxu3CgG4kkEQBQ=='
        ])->post('https://fawn-excited-ape.ngrok-free.app/api/v2/checks', [
            "description" => "string",
            "name" => "CUBSIEDN704",
            "orgID" => "6acbe12e221164c8",
            "query" => [
                "builderConfig" => [
                    "aggregateWindow" => [
                        "fillValues" => false,
                        "period" => "1m"
                    ],
                    "buckets" => [
                        "Vortex"
                    ],
                    "functions" => [
                        [
                            "name" => "mean"
                        ]
                    ],
                    "tags" => [
                        [
                            "aggregateFunctionType" => "filter",
                            "key" => "_measurement",
                            "values" => [
                                "singlegasSensor"
                            ]
                        ],
                        [
                            "aggregateFunctionType" => "filter",
                            "key" => "Model",
                            "values" => [
                                "Gen5"
                            ]
                        ],
                        [
                            "aggregateFunctionType" => "filter",
                            "key" => "_field",
                            "values" => [
                                "Hydrogen"
                            ]
                        ]
                    ]
                ],
                "editMode" => "builder",
                "name" => "TestTask1",
                "text" => 'from(bucket: "Vortex") |> range(start: -15m, stop: now()) |> filter(fn: (r) => r["_measurement"] == "singlegasSensor") |> filter(fn: (r) => r["Model"] == "Gen5") |> filter(fn: (r) => r["_field"] == "Hydrogen")'
            ],
            "status" => "active",
            "every" => "5s",
            "offset" => "0m",
            "statusMessageTemplate" => '${ r._check_name },${ r._level },${ r._time },${string(v: r.Hydrogen)}',
            "thresholds" => [
                [
                    "allValues" => true,
                    "level" => "OK",
                    "type" => "lesser",
                    "value" => 50
                ],
                [
                    "allValues" => true,
                    "level" => "WARN",
                    "type" => "range",
                    "max" => 99,
                    "min" => 50,
                    "within" => true
                ],
                [
                    "allValues" => true,
                    "level" => "CRIT",
                    "type" => "greater",
                    "value" => 100
                ]
            ],
            "type" => "threshold"
        ]);

       // Create a response array containing the message and the JSON data
        $responseData = [
            'message' => 'Your message here',
            'data' => $response->json(),
        ];

        // Return the response array encoded as JSON
        return response()->json($responseData);
    }

    public function createnotifRule(){
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer pByeCOnFddNXNNpjXeW-yKHER72jooR0w3b3iPad-AC9B75uAwW6YWoYXHwzsigQjVJmLiKPdxu3CgG4kkEQBQ=='
        ])->post('https://fawn-excited-ape.ngrok-free.app/api/v2/notificationRules', [
            "endpointID" => "0c92d5ebf108a000",
            "every" => "5s",
            "name" => "vortex_dev",
            "offset" => "0s",
            "orgID" => "6acbe12e221164c8",
            "status" => "active",
            "statusRules" => [
                [
                    "count" => 0,
                    "currentLevel" => "ANY"
                ]
            ],
            "tagRules" => [
                [
                    "key" => "_check_id",
                    "operator" => "equal",
                    "value" => "0c96292dc6e0a000"
                ]
            ],
            "type" => "http",
            "messageTemplate" => '${r._message}'
        ]);
        
        // Construct the response data
        $responseData = [
            'message' => 'Your message here',
            'data' => $response->json(),
        ];
        
        // Return the response array encoded as JSON
        return response()->json($responseData);
    }

}
