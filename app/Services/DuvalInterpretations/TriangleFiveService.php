<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleFiveService extends DuvalInterpretationService implements DuvalInterpretationInterface
{
    private $regions = [
        "PD" => array(array(214.82916666666665, 49.84909090909095), array(191.975, 87.24454545454545), array(191.975, 93.22781818181817), array(216.65750000000003, 52.840727272727264)),
        // there are 2 O polygons
        "O-1" => array(array(219.4, 42.36990700363636), array(214.82916666666665, 49.84909090909095), array(216.65750000000003, 52.840727272727264), array(191.975, 93.22781818181817), array(210.25833333333335, 117.16436363636362), array(237.68333333333334, 72.28636363636362)),
        "O-2" => array(array(120.67000000000002, 203.92181818181817), array(36.56666666666666, 341.54231922545455), array(73.13333333333333, 341.54231922545455), array(138.95333333333332, 233.84025454545457)),
        // there are 2 T3 polygons
        "T3-1" => array(array(283.3916666666667, 147.08072727272727), array(261.45166666666665, 182.98036363636368), array(288.87666666666667, 227.85663636363637), array(285.22, 233.84025454545457), array(321.78666666666663, 293.6709090909091), array(292.5333333333333, 341.54231922545455), array(402.23333333333335, 341.54231922545455)),
        "T3-2" => array(array(228.54166666666669, 236.83189090909093), array(164.55, 341.54231922545455), array(292.5333333333333, 341.54231922545455)),
        "T2" => array(array(237.68333333333334, 72.28636363636362), array(215.74333333333334, 108.18945454545455), array(261.45166666666665, 182.98036363636368), array(283.3916666666667, 147.08072727272727)),
        "S" => array(array(191.975, 87.24454545454545), array(120.67000000000002, 203.92181818181817), array(138.95333333333332, 233.84025454545457), array(210.25833333333335, 117.16436363636362)),
        "C" => array(array(215.74333333333334, 108.18945454545455), array(182.83333333333334, 162.0389090909091), array(292.5333333333333, 341.54231922545455), array(321.78666666666663, 293.6709090909091), array(285.22, 233.84025454545457), array(288.87666666666667, 227.85663636363637)),
        "ND" => array(array(182.83333333333334, 162.0389090909091), array(73.13333333333333, 341.54231922545455), array(164.55, 341.54231922545455), array(228.54166666666669, 236.83189090909093)),
    ];


    public function getResult()
    {
        $ppmTotal = $this->totalPpm($this->ethane, $this->ethylene, $this->methane);
        $proc1 = $this->firstProc($this->ethane, $ppmTotal);
        $proc2 = $this->secondProc($this->ethylene, $ppmTotal);
        $proc3 = $this->thirdProc($this->methane, $ppmTotal);
        $check = $this->normalCheck($this->ethane_roc, $this->ethylene_roc, $this->methane_roc, $this->ethane, $this->ethylene, $this->methane);
        $code =  $this->duvalCode($check, $proc1, $proc2, $proc3);

        return $code;
    }

    private function duvalCode($check, $proc1, $proc2, $proc3)
    {
        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 19-JAN-2024
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc1 <= 15 && $proc1 >= 2.5 && $proc2 > 0 && $proc2 < 1) {
        //     return "PD";
        // } else if ($proc1 > 15 && $proc1 <= 54 && $proc2 <= 10 && $proc2 >= 0) {
        //     return "S";
        // } else if ($proc1 >= 0 && $proc1 < 100 && $proc2 <= 10 && $proc2 >= 0) {
        //     return "O";
        // } else if ($proc1 >= 0 && $proc1 <= 12 && $proc2 > 10 && $proc2 <= 35) {
        //     return "T2";
        // } else if (($proc1 > 12 && $proc1 <= 30 && $proc2 > 10 && $proc2 <= 50) || ($proc1 > 14 && $proc1 <= 30 && $proc2 > 50 && $proc2 <= 70)) {
        //     return "C";
        // } else if ($proc2 > 35 && $proc1 >= 0 && $proc1 < 65 && $proc3 >= 0) {
        //     return "T3";
        // } else {
        //     return "ND";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc1 <= 15 && $proc1 > 2.5 && $proc2 <= 1) {
        //     return "PD";
        // } else if (($proc2 <= 10) && ($proc1 <= 15 || $proc1 > 54)) {
        //     return "O";
        // } else if ($proc2 <= 10) {
        //     return "S";
        // } else if (($proc1 > 12 && $proc1 <= 14 && $proc2 <= 50) || ($proc1 > 14 && $proc1 <= 30 && $proc2 <= 70)) {
        //     return "C";
        // } else if ($proc2 > 35) {
        //     return "T3";
        // } else if ($proc2 > 10 && $proc1 <= 12) {
        //     return "T2";
        // } else {
        //     return "ND";
        // }

        if($check == 1){
            return "Normal";
        }

        $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
        $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
        $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
        $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

        $c2h4_pc = $proc2;
        $c2h6_pc = $proc1;
        $ch4_pc = $proc3;

        $helper = new SVGHelpers;
        // determining the x and y coordinates of the points
        $dt1_x = ($c2h4_pc - $c2h6_pc) * sin(30 * pi() / 180);
        $dt1_y = ($ch4_pc / 100) * $tr_h + $tr_ymin;
        // before the points can be plotted the coordinates should be scalled/translated 
        // to the proper quadrant and domain where the triangle is located in the plane
        $x = $helper->scaling_x($dt1_x);
        $y = $helper->scaling_y($dt1_y);

        $point = array($x, $y);

        // Check if the point is inside each polygon
        if ($helper->pointInPolygon($point, $this->regions['PD'])) {
            return "PD";
        } elseif ($helper->pointInPolygon($point, $this->regions['O-1'])) {
            return "O";
        } elseif ($helper->pointInPolygon($point, $this->regions['O-2'])) {
            return "O";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3-1'])) {
            return "T3";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3-2'])) {
            return "T3";
        } elseif ($helper->pointInPolygon($point, $this->regions['T2'])) {
            return "T2";
        } elseif ($helper->pointInPolygon($point, $this->regions['S'])) {
            return "S";
        } elseif ($helper->pointInPolygon($point, $this->regions['C'])) {
            return "C";
        } elseif ($helper->pointInPolygon($point, $this->regions['ND'])) {
            return "ND";
        } else {
            return "N/A";
        }

        
    }

    private function normalCheck($ethane_roc, $ethylene_roc, $methane_roc, $ethane, $ethylene, $methane)
    {
        $ethanelowerlimit = 111;
        $ethylenelowerlimit = 87;
        $methanelowerlimit = 85;
        $ethaneroclowerlimit = 1.2;
        $ethyleneroclowerlimit = 1;
        $methaneroclowerlimit = 1.3;

        if ($ethane_roc <= $ethaneroclowerlimit && $ethylene_roc <= $ethyleneroclowerlimit && $methane_roc <= $methaneroclowerlimit && $ethane <= $ethanelowerlimit && $ethylene <= $ethylenelowerlimit && $methane <= $methanelowerlimit) {
            return 1;
        } else {
            return 0;
        }
    }

    // private function normalCheck($ethane,$ethylene,$methane){
    //     $ethaneUpperlimit = 210;
    //     $ethyleneUpperlimit = 270;
    //     $methaneUpperlimit = 135;

    //     if ($methane < $methaneUpperlimit && $ethane < $ethaneUpperlimit && $ethylene < $ethyleneUpperlimit){
    //         return "1";
    //     }
    //     else{
    //         return "0";
    //     }
    // }

    private function firstProc($ethane, $ppmTotal)
    {
        $result = 100 * $ethane / $ppmTotal;
        return $result;
    }
    private function secondProc($ethylene, $ppmTotal)
    {
        $result = 100 * $ethylene / $ppmTotal;
        return $result;
    }
    private function thirdProc($methane, $ppmTotal)
    {
        $result = 100 * $methane / $ppmTotal;
        return $result;
    }

    private function totalPpm($ethane, $ethylene, $methane)
    {
        $computation = $ethane + $ethylene + $methane;
        if ($computation == 0) {
            return .00000000001;
        } else {
            return $computation;
        }
    }
}
