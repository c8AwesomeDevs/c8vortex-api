<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleThreeService extends DuvalInterpretationService implements DuvalInterpretationInterface
{
    private $regions = [
        "PD" => array(array(219.4, 42.36990700363636), array(215.74333333333334, 48.35327272727273), array(223.05666666666667, 48.35327272727273)),
        "T1" => array(array(215.74333333333334, 48.353355256363685), array(212.08666666666667, 54.336803509090885), array(308.98833333333334, 212.89818197145456), array(316.3016666666667, 200.93128547636366), array(223.05666666666667, 48.353355256363685)),
        "T2" => array(array(316.3016666666667, 200.93128547636366), array(308.98833333333334, 212.89818197145456), array(360.1816666666667, 296.6664573818182), array(367.495, 284.6995609109091)),
        "T3" => array(array(367.495, 284.6995609109091), array(402.23333333333335, 341.54231922545455), array(332.75666666666666, 341.54231922545455)),
        "DT" => array(array(212.08666666666667, 54.336803509090885), array(360.1816666666667, 296.6664573818182), array(332.75666666666666, 341.54231922545455), array(296.19000000000005, 341.54231922545455), array(239.51166666666666, 248.798871436), array(268.76500000000004, 200.93128547636366), array(195.6316666666667, 81.26232059454546)),
        "D1" => array(array(195.6316666666667, 81.26232059454546), array(232.19833333333335, 141.0968030527273), array(109.7, 341.54231922545455), array(36.56666666666666, 341.54231922545455)),
        "D2" => array(array(296.19000000000005, 341.54231922545455), array(239.51166666666666, 248.798871436), array(268.76500000000004, 200.93128547636366), array(232.19833333333335, 141.0968030527273), array(109.7, 341.54231922545455))
    ];

    public function getResult()
    {
        $ppmTotal = $this->totalPpm($this->acetylene, $this->ethylene, $this->methane);
        $proc1 = $this->firstProc($this->acetylene, $ppmTotal);
        $proc2 = $this->secondProc($this->ethylene, $ppmTotal);
        $proc3 = $this->thirdProc($this->methane, $ppmTotal);
        $check = $this->normalCheck($this->acetylene_roc, $this->ethylene_roc, $this->methane_roc, $this->acetylene, $this->ethylene, $this->methane);
        $code =  $this->duvalCode($check, $proc1, $proc2, $proc3);

        return $code;
    }

    private function duvalCode($check, $proc1, $proc2, $proc3)
    {
        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 17-OCT-2023
        // if ($proc3 <= 120 && $proc2 <= 50 && $this->acetylene <= 1) {
        //     return "Normal";
        // } else if ($proc3 >= 98 && $proc3 <= 100) {
        //     return "PD";
        // } else if ($proc2 <= 20 && $proc1 >= 13) {
        //     return "D1";
        // } else if (($proc2 > 20 && $proc2 <= 40 && $proc1 >= 13) || ($proc2 <= 40 && $proc1 >= 29)) {
        //     return "D2";
        // } else if ($proc1 <= 4 && $proc2 <= 52) {
        //     return "T1";
        // } else if ($proc1 <= 4 && $proc2 > 52 && $proc2 < 81) {
        //     return "T2";
        // } else if ($proc2 >= 81 && $proc2 <= 100) {
        //     return "T3";
        // } else {
        //     return "DT";
        // }

        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 18-JAN-2024
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 > 98) {
        //     return "PD";
        // } else if ($proc1 <= 13 && $proc2 <= 20) {
        //     return "D1";
        // } else if ($proc1 <= 13 && $proc2 > 20 && ($proc2 <= 40 || $proc1 > 29)) {
        //     return "D2";
        // } else if ($proc1 > 4 && $proc2 > 20) {
        //     return "DT";
        // } else if ($proc1 <= 4 && $proc2 <= 53) {
        //     return "T1";
        // } else if ($proc1 <= 4 && $proc2 <= 81 && $proc2 > 53) {
        //     return "T2";
        // } else {
        //     return "T3";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 > 98) {
        //     return "PD";
        // } else if ($proc1 > 13 && $proc2 <= 20) {
        //     return "D1";
        // } else if (($proc1 > 13 && $proc2 > 20 && $proc2 <= 40) || ($proc2 > 40 && $proc1 > 29)) {
        //     return "D2";
        // } else if ($proc2 > 81) {
        //     return "T3";
        // } else if ($proc2 > 4) {
        //     return "DT";
        // } else if ($proc2 > 53) {
        //     return "T2";
        // } else {
        //     return "T1";
        // }
        if ($check == 1) {
            return "Normal";
        } 

        // setting the configs of the triangle; these will used to plot the points inside the triangle
        $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
        $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
        $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
        $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

        $c2h4_pc = $proc2;
        $c2h2_pc = $proc1;
        $ch4_pc =$proc3;

        $helper = new SVGHelpers;
        // determining the x and y coordinates of the points
        $dt1_x = ($c2h4_pc - $c2h2_pc) * sin(30 * pi() / 180);
        $dt1_y = ($ch4_pc / 100) * $tr_h + $tr_ymin;
        // before the points can be plotted the coordinates should be scalled/translated 
        // to the proper quadrant and domain where the triangle is located in the plane
        $x = $helper->scaling_x($dt1_x);
        $y = $helper->scaling_y($dt1_y);

        $point = array($x, $y);

        // Check if the point is inside each polygon
        if ($helper->pointInPolygon($point, $this->regions['PD'])) {
            return "PD";
        } elseif ($helper->pointInPolygon($point, $this->regions['T1'])) {
            return "T1";
        } elseif ($helper->pointInPolygon($point, $this->regions['T2'])) {
            return "T2";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3'])) {
            return "T3";
        } elseif ($helper->pointInPolygon($point, $this->regions['DT'])) {
            return "DT";
        } elseif ($helper->pointInPolygon($point, $this->regions['D1'])) {
            return "D1";
        } elseif ($helper->pointInPolygon($point, $this->regions['D2'])) {
            return "D2";
        } else {
            return "N/A";
        }
    }

    private function normalCheck($acetylene_roc, $ethylene_roc, $methane_roc, $acetylene, $ethylene, $methane)
    {
        $acetylenelowerlimit = 5;
        $ethylenelowerlimit = 87;
        $methanelowerlimit = 85;
        $acetyleneroclowerlimit = 1;
        $ethyleneroclowerlimit = 1;
        $methaneroclowerlimit = 1.3;

        if ($methane_roc < $methaneroclowerlimit && $ethylene_roc < $ethyleneroclowerlimit && $acetylene_roc < $acetyleneroclowerlimit && $methane < $methanelowerlimit && $ethylene < $ethylenelowerlimit && $acetylene < $acetylenelowerlimit) {
            return 1;
        } else {
            return 0;
        }
    }

    // private function normalCheck($acetylene,$ethylene,$methane){
    //     $acetyleneUpperlimit = 19;
    //     $ethyleneUpperlimit = 270;
    //     $methaneUpperlimit = 135;

    //     if ($methane < $methaneUpperlimit && $ethylene < $ethyleneUpperlimit && $acetylene < $acetyleneUpperlimit){
    //         return "1";
    //     }
    //     else{
    //         return "0";
    //     }
    // }

    private function firstProc($acetylene, $ppmTotal)
    {
        $result = 100 * $acetylene / $ppmTotal;
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

    private function totalPpm($acetylene, $ethylene, $methane)
    {
        $computation = $acetylene + $ethylene + $methane;
        if ($computation == 0) {
            return .00000000001;
        } else {
            return $computation;
        }
    }
}
