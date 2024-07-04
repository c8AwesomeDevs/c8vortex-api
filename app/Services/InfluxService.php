<?php

namespace App\Services;

use App\Contracts\InfluxInterface;
use App\Models\Events;
use App\Models\Checks;
use Carbon\Carbon;

class InfluxService implements InfluxInterface
{
    public function clean_results($tags, $datas)
    {
        // seperate the each element of 'datas' per tagname/_field
        // end-result: an array where each element is an array with uniform '_field' attribute
        $segmentedPerTag = [];
        foreach ($tags as $key => $value) {
            $filtered = array_filter($datas, function ($val) use ($value) {
                return $val['_field'] == $value['_field'];
            });
            array_push($segmentedPerTag, array_values($filtered));
        }

        // checksum the timestamps; make sure that they are all the same
        $concatinated_times = [];
        foreach ($segmentedPerTag as $key => $value) {
            $concats = "";
            foreach ($segmentedPerTag[$key] as $key1 => $value1) {
                $concats .= $segmentedPerTag[$key][$key1]['_time'];
            }
            array_push($concatinated_times, $concats);
        }
        $refhash = hash('murmur3c', $concatinated_times[0], false);
        $check_passed = true;
        foreach ($concatinated_times as $key => $value) {
            $xhash = hash('murmur3c', $concatinated_times[$key], false);
            if ($refhash != $xhash) {
                $check_passed = false;
                break;
            }
        }

        if ($check_passed) {
            $rearranged = [];
            // use the timestamp of the first element of $segmentedPerTag
            // this is safe to do because we did the check on timestamps previously
            foreach ($segmentedPerTag[0] as $key => $value) {
                array_push($rearranged, ['_time' => $segmentedPerTag[0][$key]['_time']]);
            }
            // format the data such that all '_fields' is inside one object that has one timestamp
            foreach ($segmentedPerTag[0] as $key1 => $value1) {
                foreach ($segmentedPerTag as $key => $value) {
                    $rearranged[$key1][$segmentedPerTag[$key][$key1]['_field']] = $segmentedPerTag[$key][$key1]['_value'];
                }
            }

            // insert some analysis before returning
            $inserted_analysis = $this->insert_analysis($rearranged);
        } else {
            return [
                'clean_status' => false,
                'message' => "Cleaning error: mismatched timestamps"
            ];
        }

        return [
            'clean_status' => true,
            'message' => "Clean success",
            'clean_data' => $inserted_analysis
        ];
    }

    public function insert_analysis($data)
    {
        foreach ($data as $key => $value) {
            // deltas are ofset-ed by 20million
            $data[$key]['DeltaDay'] = intval($data[$key]['DeltaDay']);
            $data[$key]['DeltaWeek'] = intval($data[$key]['DeltaWeek']);
            $data[$key]['DeltaMonth'] = intval($data[$key]['DeltaMonth']);
            // states should be integers
            $data[$key]['BatteryBackupError'] = intval($data[$key]['BatteryBackupError']);
            $data[$key]['ConfigurationDataNotValid'] = intval($data[$key]['ConfigurationDataNotValid']);
            $data[$key]['DataAvailable'] = intval($data[$key]['DataAvailable']);
            $data[$key]['Error'] = intval($data[$key]['Error']);
            $data[$key]['HeaterFault'] = intval($data[$key]['HeaterFault']);
            $data[$key]['HydrogenSensorFault'] = intval($data[$key]['HydrogenSensorFault']);
            $data[$key]['PCBTempOver105C'] = intval($data[$key]['PCBTempOver105C']);
            $data[$key]['RequiredDataNA'] = intval($data[$key]['RequiredDataNA']);
            $data[$key]['SensorState'] = intval($data[$key]['SensorState']);
            $data[$key]['TemperatureSensorFault'] = intval($data[$key]['TemperatureSensorFault']);
            $data[$key]['UnitReady'] = intval($data[$key]['UnitReady']);
            // readings are floats
            $data[$key]['Hydrogen'] = floatval($data[$key]['Hydrogen']);
            $data[$key]['OilTemperature'] = floatval($data[$key]['OilTemperature']);
            $data[$key]['PCBTemperature'] = floatval($data[$key]['PCBTemperature']);
        }
        return $data;
    }

    // public function compute_InfluxDelta($value)
    // {
    //     return 20000000 - floatval($value);
    // }

    public function getEvents($check_name, $startTime = null, $endTime = null){
        
        $events_data = Events::where('event_id', $check_name)
                        ->where('start_time', '>=', $startTime)
                        ->where('start_time', '<=', $endTime)
                        ->orderBy('start_time', 'desc');

        return $events_data->get();
    }

    public function getcurrentEvent($check_name){
          // Fetch the latest event for the given event_name
        $latestEvent = Events::where('event_id', $check_name)
                        ->orderBy('start_time', 'desc')
                        ->first();
        
        return $latestEvent;
    }

    public function saveEvents($events){
        
        $new_event = Events::create($events);
        
        return $new_event;
    }

    public function getCheckname($transformer_id){

        $check_name = Checks::where('transformer_id', $transformer_id);

        return $check_name->get();
    }
    
    public function convertToLocalizedDateTime($dateTimeString) {
        $carbonDateTime = Carbon::parse($dateTimeString);
        $localizedDateTime = $carbonDateTime->addHours(8)->setTimezone(config('app.timezone'));
        return $localizedDateTime->toDateTimeString(); // or any other format
    }
    
    public function changeLevel($level){
        if($level == 'crit'){
            return 'critical';
        }else if($level == 'warn'){
            return 'normal';
        }else if ($level == 'ok'){
            return 'healthy';
        }
    }

}
