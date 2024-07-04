<?php

namespace App\Traits;

trait NormalCheckerTrait {
    // public function getStatus($data) {
    //     //Upper Limit
    //     $acetyleneUpperlimit = 19;
    //     $ethyleneUpperlimit = 270;
    //     $methaneUpperlimit = 135;
    //     $ethaneUpperlimit = 210;
    //     $hydrogenUpperlimit = 200;
    
    //     $errors=[];
    
    //     if($data->acetylene < $acetyleneUpperlimit && 
    //         $data->ethylene < $ethyleneUpperlimit && 
    //         $data->methane < $methaneUpperlimit && 
    //         $data->ethane < $ethaneUpperlimit && 
    //         $data->hydrogen < $hydrogenUpperlimit){
    //         return "Normal";
    //     } else{
    //         if($data->acetylene > $acetyleneUpperlimit)
    //             $errors[] = "Acetylene (".$data->acetylene.") reached the Upper limit";
    //         if($data->ethylene > $ethyleneUpperlimit)
    //             $errors[] = "Ethylene (".$data->ethylene.") reached the Upper limit";
    //         if($data->methane > $methaneUpperlimit)
    //             $errors[] = "Methane (".$data->methane.") reached the Upper limit";
    //         if($data->ethane > $ethaneUpperlimit)
    //             $errors[] = "Ethane (".$data->ethane.") reached the Upper limit";
    //         if($data->hydrogen > $hydrogenUpperlimit)
    //             $errors[] = "Hydrogen (".$data->hydrogen.") reached the Upper limit";
    //         return $errors;
    //     }
    // }   

    public function getStatus($data) {

        //Upper Limits
        $upperLimits = [
            'acetylene' => 5,
            'ethylene' => 87,
            'methane' => 85,
            'ethane' => 111,
            'hydrogen' => 119,
        ];
        $errors=[];
        foreach ($upperLimits as $property => $limit) {
            if ($data->$property > $limit) {
                $errors[] = ucfirst($property) . " ({$data->$property}) reached the Upper limit";
            }
        }

        $response = [
            'normal' => count($errors) ? false : true,
            'error_details' => $errors
        ];

        return $response;
    }
    
}