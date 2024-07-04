<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleOneService extends DuvalInterpretationService implements DuvalInterpretationInterface
{
    private $regions = [
        "PD" => array(array(219.4, 42.36990700363636), array(215.74333333333334, 48.35327272727273), array(223.05666666666667, 48.35327272727273)),
        "T1" => array(array(223.05666666666667, 48.35327272727273), array(215.74333333333334, 48.35327272727273), array(212.08666666666667, 54.336545454545444), array(248.65333333333334, 114.17272727272726), array(255.9666666666667, 102.20272727272732)),
        "T2" => array(array(255.9666666666667, 102.20272727272732), array(248.65333333333334, 114.17272727272726), array(303.50333333333333, 203.92300962000002), array(310.81666666666666, 191.95611311454547)),
        "T3" => array(array(310.81666666666666, 191.95611311454547), array(283.3916666666667, 236.83197494781817), array(347.3833333333333, 341.54231922545455), array(402.23333333333335, 341.54231922545455)),
        "DT" => array(array(212.08666666666667, 54.336545454545444), array(195.6316666666667, 81.26232059454546), array(268.76500000000004, 200.93128547636366), array(239.51166666666666, 248.798871436), array(296.19000000000005, 341.54231922545455), array(347.3833333333333, 341.54231922545455), array(283.3916666666667, 236.83197494781817), array(303.50333333333333, 203.92300962000002)),
        "D1" => array(array(195.6316666666667, 81.26232059454546), array(36.56666666666666, 341.54231922545455), array(120.67000000000002, 341.54231922545455), array(237.68333333333334, 150.07197541454545)),
        "D2" => array(array(237.68333333333334, 150.07197541454545), array(120.67000000000002, 341.54231922545455), array(296.19000000000005, 341.54231922545455), array(239.51166666666666, 248.798871436), array(268.76500000000004, 200.93128547636366))
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
        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 18-JAN-2024
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 >= 98 && $proc3 <= 100) {
        //     return "PD";
        // } else if ($proc2 <= 23 && $proc1 >= 13) {
        //     return "D1";
        // } else if (($proc2 > 23 && $proc2 <= 40 && $proc1 >= 13) || ($proc2 >= 40 && $proc1 >= 29)) {
        //     return "D2";
        // } else if ($proc1 <= 4 && $proc2 < 20) {
        //     return "T1";
        // } else if ($proc1 <= 4 && $proc2 >= 20 && $proc2 < 50) {
        //     return "T2";
        // } else if ($proc1 < 15 && $proc2 >= 50) {
        //     return "T3";
        // } else {
        //     return "DT";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc2 >= 98) {
        //     return "PD";
        // } else if ($proc1 <= 23 && $proc1 > 13) {
        //     return "D1";
        // } else if (($proc1 > 24 && $proc2 <= 40 && $proc1 > 13) || ($proc2 > 40 && $proc1 > 29)) {
        //     return "D2";
        // } else if ($proc1 <= 15 && $proc2 > 50) {
        //     return "T3";
        // } else if ($proc2 > 4) {
        //     return "DT";
        // } else if ($proc2 > 20) {
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
        $ch4_pc = $proc3;

        // determining the x and y coordinates of the points
        $dt1_x = ($c2h4_pc - $c2h2_pc) * sin(30 * pi() / 180);
        $dt1_y = ($ch4_pc / 100) * $tr_h + $tr_ymin;
        // before the points can be plotted the coordinates should be scalled/translated 
        // to the proper quadrant and domain where the triangle is located in the plane
        $helper = new SVGHelpers;
        $x = $helper->scaling_x($dt1_x);
        $y = $helper->scaling_y($dt1_y);
        $point = array($x, $y);
        
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

        // there is another (easier?) way of classifiying what region/zone a datapoint landed on
        // it is called the "point-on-polygon" algorithm; however this algo requires the coordinates of
        // both all the polygons (D1,D2,T3,etc..) and all the datapoints; this remains to be the main hurdle
        // as to why we are still using "if-else-if"
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
