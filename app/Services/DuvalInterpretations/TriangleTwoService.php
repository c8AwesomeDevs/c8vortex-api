<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleTwoService extends DuvalInterpretationService implements DuvalInterpretationInterface
{

     // these coordinates was extracted from the generated svg
     private $regions = [
        "X1" => array(array(219.4, 42.36990700363636), array(261.45166666666665, 111.17956182363639), array(155.40833333333333, 284.6995609109091), array(71.305, 284.6995609109091)),
        "X3" => array(array(234.02666666666667, 156.0554236327273), array(347.3833333333333, 341.54231922545455), array(120.67000000000002, 341.54231922545455)),
        "T2" => array(array(261.45166666666665, 111.17956182363639), array(234.02666666666667, 156.0554236327273), array(283.3916666666667, 236.83197494781817), array(310.81666666666666, 191.95611311454547)),
        "T3" => array(array(283.3916666666667, 236.83197494781817), array(310.81666666666666, 191.95611311454547), array(402.23333333333335, 341.54231922545455), array(347.3833333333333, 341.54231922545455)),
        "D1" => array(array(93.245, 284.6995609109091), array(71.305, 284.6995609109091), array(36.56666666666666, 341.54231922545455), array(120.67000000000002, 341.54231922545455), array(124.32666666666667, 335.5588709727273), array(62.163333333333334, 335.5588709727273)),
        "N" => array(array(155.40833333333333, 284.6995609109091), array(93.245, 284.6995609109091), array(62.163333333333334, 335.5588709727273), array(124.32666666666667, 335.5588709727273)),
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
        // if ($check == 1){
        //     return "Normal";
        // }
        // else if($proc2 <= 13 && $proc3 >= 19){
        //     return "X1";
        // }
        // else if($proc2 > 13 && $proc2 <= 50 && $proc1 <= 15){
        //     return "T2";
        // }
        // else if($proc2 > 50 && $proc1 <= 15){
        //     return "T3";
        // }
        // else if($proc1 > 15 && $proc2 > 13){
        //     return "X3";
        // }
        // else if($proc3 >= 2 && $proc3 < 19 && $proc2 >= 6 && $proc2 <= 13){
        //     return "N";
        // }
        // else{
        //     return "D1";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 > 2 && $proc3 <= 9 && $proc2 > 6 && $proc2 <= 13) {
        //     return "N";
        // } else if ($proc3 <= 9 && $proc2 <= 13) {
        //     return "D1";
        // } else if ($proc2 <= 13) {
        //     return "X1";
        // } else if ($proc1 > 15) {
        //     return "X3";
        // } else if ($proc2 > 50) {
        //     return "T3";
        // } else {
        //     return "T2";
        // }

        if ($check == 1){
            return "Normal";
        }

        $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
        $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
        $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
        $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

        $c2h4_pc = $proc2;
        $c2h2_pc = $proc1;
        $ch4_pc = $proc3;

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
        if ($helper->pointInPolygon($point, $this->regions['X1'])) {
            return "X1";
        } elseif ($helper->pointInPolygon($point, $this->regions['X3'])) {
            return "X3";
        } elseif ($helper->pointInPolygon($point, $this->regions['T2'])) {
            return "T2";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3'])) {
            return "T3";
        } elseif ($helper->pointInPolygon($point, $this->regions['D1'])) {
            return "D1";
        } elseif ($helper->pointInPolygon($point, $this->regions['N'])) {
            return "N";
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
