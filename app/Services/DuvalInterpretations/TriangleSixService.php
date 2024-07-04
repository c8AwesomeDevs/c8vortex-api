<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleSixService extends DuvalInterpretationService implements DuvalInterpretationInterface
{

    private $regions = [
        "S" => array(array(219.4, 42.36990700363636), array(241.34000000000003, 78.27059648545453), array(80.44666666666666, 341.54231922545455), array(36.56666666666666, 341.54231922545455)),
        "C" => array(array(285.22, 150.07197541454545), array(241.34000000000003, 221.87335433672726), array(314.47333333333336, 341.54231922545455), array(402.23333333333335, 341.54231922545455)),
        "O" => array(array(298.0183333333334, 314.61680214), array(314.47333333333336, 341.54231922545455), array(80.44666666666666, 341.54231922545455), array(96.90166666666667, 314.61680214)),
        "ND" => array(array(241.34000000000003, 78.27059648545453), array(96.90166666666667, 314.61680214), array(298.0183333333334, 314.61680214), array(241.34000000000003, 221.87335433672726), array(285.22, 150.07197541454545)),
        "PD" => array(array(223.05666666666667, 48.353355256363685), array(221.22833333333332, 51.345079365454524), array(244.99666666666667, 90.23749295636362), array(246.82500000000002, 87.24576884727274)),
    ];

    public function getResult()
    {
        $ppmTotal = $this->totalPpm($this->methane, $this->hydrogen, $this->ethane);
        $proc1 = $this->firstProc($this->methane, $ppmTotal);
        $proc2 = $this->secondProc($this->hydrogen, $ppmTotal);
        $proc3 = $this->thirdProc($this->ethane, $ppmTotal);
        $check = $this->normalCheck($this->ethane_roc, $this->hydrogen_roc, $this->methane_roc, $this->ethane, $this->hydrogen, $this->methane);
        $code =  $this->duvalCode($check, $proc1, $proc2, $proc3);
        return $code;
    }

    private function duvalCode($check, $proc1, $proc2, $proc3)
    {
        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 17-OCT-2023
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc1 >= 2 && $proc3 <= 1 && $proc1 <= 15) {
        //     return "PD";
        // } else if ($proc1 > 2 && $proc1 <= 12 && $proc3 > 1) {
        //     return "S";
        // } else if ($proc3 <= 24 && $proc1 >= 36) {
        //     return "C";
        // } else if ($proc1 > 12 && $proc2 <= 9 && $proc3 > 24) {
        //     return "O";
        // } else {
        //     return "ND";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 <= 2 && $proc1 > 2 && $proc1 > 15) {
        //     return "PD";
        // } else if ($proc1 <= 12) {
        //     return "S";
        // } else if ($proc2 <= 9 && $proc3 > 24) {
        //     return "O";
        // } else if ($proc1 > 36 && $proc3 <= 24) {
        //     return "C";
        // } else {
        //     return "ND";
        // }

        if($check == 1){
            return "Normal";
        }
         // setting the configs of the triangle; these will used to plot the points inside the triangle
         $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
         $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
         $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
         $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

         $ch4_pc = $proc1;
         $c2h6_pc = $proc3;
         $h2_pc = $proc2;

         $helper = new SVGHelpers;
         // determining the x and y coordinates of the points
         $dt1_x = ($ch4_pc - $c2h6_pc) * sin(30 * pi() / 180);
         $dt1_y = ($h2_pc / 100) * $tr_h + $tr_ymin;
         // before the points can be plotted the coordinates should be scalled/translated 
         // to the proper quadrant and domain where the triangle is located in the plane
         $x = $helper->scaling_x($dt1_x);
         $y = $helper->scaling_y($dt1_y);

         $point = array($x, $y);

        // Check if the point is inside each polygon
        if ($helper->pointInPolygon($point, $this->regions['S'])) {
            return "S";
        } elseif ($helper->pointInPolygon($point, $this->regions['C'])) {
            return "C";
        } elseif ($helper->pointInPolygon($point, $this->regions['O'])) {
            return "O";
        } elseif ($helper->pointInPolygon($point, $this->regions['ND'])) {
            return "ND";
        } elseif ($helper->pointInPolygon($point, $this->regions['PD'])) {
            return "PD";
        } else {
            return "N/A";
        }
    }

    private function normalCheck($ethane_roc, $hydrogen_roc, $methane_roc, $ethane, $hydrogen, $methane)
    {
        $ethanelowerlimit = 111;
        $hydrogenlowerlimit = 119;
        $methanelowerlimit = 85;
        $ethaneroclowerlimit = 1.2;
        $hydrogenroclowerlimit = 1.8;
        $methaneroclowerlimit = 1.3;

        if ($ethane_roc <= $ethaneroclowerlimit && $hydrogen_roc <= $hydrogenroclowerlimit && $methane_roc <= $methaneroclowerlimit && $ethane <= $ethanelowerlimit && $hydrogen <= $hydrogenlowerlimit && $methane <= $methanelowerlimit) {
            return 1;
        } else {
            return 0;
        }
    }

    // private function normalCheck($ethane,$hydrogen,$methane){
    //     $ethaneUpperlimit = 210;
    //     $hydrogenUpperlimit = 200;
    //     $methaneUpperlimit = 135;

    //     if ($ethane <= $ethaneUpperlimit && $hydrogen <= $hydrogenUpperlimit && $methane <= $methaneUpperlimit){
    //         return "1";
    //     }
    //     else{
    //         return "0";
    //     }
    // }

    private function firstProc($methane, $ppmTotal)
    {
        $result = 100 * $methane / $ppmTotal;
        return $result;
    }
    private function secondProc($hydrogen, $ppmTotal)
    {
        $result = 100 * $hydrogen / $ppmTotal;
        return $result;
    }
    private function thirdProc($ethane, $ppmTotal)
    {
        $result = 100 * $ethane / $ppmTotal;
        return $result;
    }

    private function totalPpm($methane, $hydrogen, $ethane)
    {
        $computation = $methane + $hydrogen + $ethane;
        if ($computation == 0) {
            return .00000000001;
        } else {
            return $computation;
        }
    }
}
