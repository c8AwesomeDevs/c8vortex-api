<?php

namespace App\Services;
use App\Models\AttributeValue;
use App\Contracts\AttributeValueInterface;
use DB;
class AttributeValueService implements AttributeValueInterface
{
    // public function getAttributeValues($element_id, $start = null, $end = null, $order = 'ASC') {
    //     $values = AttributeValue::where('element_id', $element_id);

    //     if($start && $end) {
    //         $values->whereBetween('timestamp', [$start, $end]);
    //     }
    //     $values->orderBy('timestamp', $order);
        
    //     return $values->get();
    // }

    public function getAttributeValues($element_id, $start = null, $end = null, $order = 'DESC') {
        $values = AttributeValue::join('elements', 'elements.id', '=', 'attribute_values.element_id')
            ->where('element_id', $element_id);
    
        if ($start && $end) {
            $values->whereBetween('timestamp', [$start, $end]);
        }
        $values->orderBy('timestamp', $order);
    
        return $values->get();
    }    

    public function deleteAllAttributeValues($company_id){

        $deleteAllAttributeValues = AttributeValue::where('company_id', $company_id)->delete();

        return $deleteAllAttributeValues;
    }

    // public function getAttributeValues($element_id, $start = null, $end = null, $order = 'ASC') {
    //     $values = AttributeValue::where('element_id', $element_id)
    //         ->join('path', 'attribute_values.element_id', '=', 'path.attribute_id');
    
    //     if($start && $end) {
    //         $values->whereBetween('timestamp', [$start, $end]);
    //     }
    //     $values->orderBy('timestamp', $order);
    
    //     $results = $values->get();
        
    //     // Loop through the results and split the element path
    //     foreach ($results as $result) {
    //         $elementPath = $result->element_path;
    //         $splitElementPath = explode('\\', $elementPath);
    //         $result->site = $splitElementPath[0];
    //         $result->substation = $splitElementPath[1];
    //         $result->transformer = $splitElementPath[2];
    //     }
    
    //     return $results;
    // }
    

    // public function getPreviousAttributeValue($element_id, $start = null, $end = null) {
    //     $value = AttributeValue::where('element_id', $element_id)
    //         ->orderBy('timestamp', 'DESC')
    //         ->where('timestamp', '<=', $end)->first();
            
        
    //     if(!is_object($value)) {
    //         $value = AttributeValue::where('element_id', $element_id)
    //             ->orderBy('timestamp', 'DESC')->skip(1)->first();
                
    //     }
        
    //     return $value;
    // }

    public function getPreviousAttributeValue($element_id, $start = null, $end = null) {
        $latest_value = AttributeValue::where('element_id', $element_id)
            ->orderBy('timestamp', 'DESC')
            ->where('timestamp', '<=', $end)
            ->first();
    
        if(!is_object($latest_value)) {
            $latest_value = AttributeValue::where('element_id', $element_id)
                ->orderBy('timestamp', 'DESC')
                ->first();
        }
    
        $previous_value = AttributeValue::where('element_id', $element_id)
            ->where('timestamp', '<', $latest_value->timestamp)
            ->orderBy('timestamp', 'DESC')
            ->first();
    
        return $previous_value;
    }

    public function getLatestAttributeValue($element_id, $start = null, $end = null) {
        $value = AttributeValue::where('element_id', $element_id)
            ->orderBy('timestamp', 'DESC')
            ->where('timestamp', '<=', $end)
            ->first();
            
        
        if(!is_object($value)) {
            $value = AttributeValue::where('element_id', $element_id)
                ->orderBy('timestamp', 'DESC')
                ->first();
                
        }
        
        return $value;
    }

    // public function getLatest($element_id, $timestamp, $start = null, $end = null){
    //     $value = AttributeValue::where('element_id', $element_id)
    //     ->orderBy('timestamp', 'ASC')
    //     ->first();
    //     return $value;
    // }

    public function getclosestPrev($element_id, $timestamp, $start = null, $end = null)
    {
        $value = AttributeValue::where('element_id', $element_id)
            ->where('timestamp', '<', $timestamp)
            ->orderBy('timestamp', 'DESC')
            ->first();
        return $value;
    }
    public function getclosestNext($element_id, $timestamp, $start = null, $end = null)
    {
        $value = AttributeValue::where('element_id', $element_id)
            ->where('timestamp', '>', $timestamp)
            ->orderBy('timestamp', 'ASC')
            ->first();
        return $value;
    }



    public function save($data) {
        $attribute_value = AttributeValue::create($data);

        return $attribute_value;
    }


    public function updateclosestNext($nextid, $data2) {
        $update = AttributeValue::where('id', $nextid)->update($data2);
        return $update;
    }

    public function dataExists($element_id, $timestamp) {
        $attribute = AttributeValue::where('timestamp', date('Y-m-d H:i:s', strtotime($timestamp)))
            ->where('element_id', $element_id);
        return $attribute->count();
    }

    public function countValuesByElementID($elem_id)
    {
        // values from the same transformer has the same $element_id
        $valueCount = AttributeValue::where('element_id', $elem_id)->count();
        return ["values" => $valueCount];
    }
}

