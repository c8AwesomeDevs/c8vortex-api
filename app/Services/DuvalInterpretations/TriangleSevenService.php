<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleSevenService extends DuvalInterpretationService implements DuvalInterpretationInterface
{
    private $regions = [
        "S" => array(array(219.4, 42.3699070072201), array(237.68333333333334, 72.2871482288644), array(122.49833333333333, 260.76576792522314), array(104.215, 230.84852670357895)),
        "O" => array(array(237.68333333333334, 72.2871482288644), array(182.83333333333334, 162.03887189379725), array(228.54166666666669, 236.8319749479078), array(283.3916666666667, 147.0802512829751)),
        "C" => array(array(283.3916666666667, 147.0802512829751), array(402.23333333333335, 341.5423192236627), array(164.55, 341.5423192236627)),
        "T3" => array(array(104.215, 230.84852670357895), array(129.81166666666667, 272.73266441388085), array(87.76, 341.5423192236627), array(36.56666666666666, 341.5423192236627)),
        "ND" => array(array(182.83333333333334, 162.03887189379725), array(228.54166666666669, 236.8319749479078), array(164.55, 341.5423192236627), array(87.76, 341.5423192236627), array(129.81166666666667, 272.73266441388085), array(122.49833333333333, 260.76576792522314)),
    ];


    public function getResult()
    {
        $ppmTotal = $this->totalPpm($this->ethane, $this->ethylene, $this->methane);
        $proc1 = $this->firstProc($this->ethane, $ppmTotal);
        $proc2 = $this->secondProc($this->ethylene, $ppmTotal);
        $proc3 = $this->thirdProc($this->methane, $ppmTotal);
        $check = $this->normalCheck($this->ethane_roc, $this->ethylene_roc, $this->methane_roc, $this->ethane, $this->ethylene, $this->methane);
        $code =  $this->duvalCode($check, $this->methane, $this->ethylene, $this->ethane, $proc1, $proc2, $proc3);
        return $code;
    }

    private function duvalCode($check, $methane, $ethylene, $ethane, $proc1, $proc2, $proc3)
    {
        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 17-OCT-2023
        // if ($methane <= 120 && $ethylene <= 50 && $ethane <= 65) {
        //     return "Normal";
        // } else if ($proc2 <= 10 && $proc1 < 64) {
        //     return "O";
        // } else if ($proc2 > 10 && $proc1 <= 30 && $proc1 < 35) {
        //     return "C";
        // } else if ($proc2 >= 35 && $proc2 <= 100) {
        //     return "T3";
        // } else if ($proc3 >= 64 && $proc1 <= 14) {
        //     return "S";
        // } else {
        //     return "ND";
        // }

        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 19-DEC-2024
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc1 <= 63 && $proc2 <= 14) {
        //     return "S";
        // } else if ($proc2 <= 10) {
        //     return "O";
        // } else if ($proc2 < 35) {
        //     return "T3";
        // } else if ($proc1 <= 30) {
        //     return "C";
        // } else {
        //     return "ND";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc1 > 63 && $proc2 <= 14) {
        //     return "S";
        // } else if ($proc2 <= 10) {
        //     return "O";
        // } else if ($proc2 > 35) {
        //     return "T3";
        // } else if ($proc1 <= 30) {
        //     return "C";
        // } else {
        //     return "ND";
        // }
        

        if ($check == 1){
            return "Normal";
        }
         // setting the configs of the triangle; these will used to plot the points inside the triangle
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
        if ($helper->pointInPolygon($point, $this->regions['S'])) {
            return "S";
        } elseif ($helper->pointInPolygon($point, $this->regions['O'])) {
            return "O";
        } elseif ($helper->pointInPolygon($point, $this->regions['C'])) {
            return "C";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3'])) {
            return "T3";
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

    //     if ($ethane <= $ethaneUpperlimit && $ethylene <= $ethyleneUpperlimit && $methane <= $methaneUpperlimit){
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
