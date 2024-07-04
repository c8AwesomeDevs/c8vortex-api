<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class TriangleFourService extends DuvalInterpretationService implements DuvalInterpretationInterface
{
    private $regions = [
        "PD" => array(array(223.05666666666667, 48.353355256363685), array(221.22833333333332, 51.345079365454524), array(244.99666666666667, 90.23749295636362), array(246.82500000000002, 87.24576884727274)),
        "S" => array(array(219.4, 42.36990700363636), array(135.29666666666668, 179.98921664363638), array(221.22833333333332, 314.61680214), array(276.0783333333333, 314.61680214), array(265.10833333333335, 296.6664573818182), array(287.04833333333335, 296.6664573818182), array(241.34000000000003, 221.87335433672726), array(285.22, 150.07197541454545), array(246.82500000000002, 87.24576884727274), array(244.99666666666667, 90.23749295636362), array(221.22833333333332, 51.345079365454524), array(223.05666666666667, 48.353355256363685)),
        "C" => array(array(285.22, 150.07197541454545), array(241.34000000000003, 221.87335433672726), array(287.04833333333335, 296.6664573818182), array(265.10833333333335, 296.6664573818182), array(292.5333333333333, 341.54231922545455), array(402.23333333333335, 341.54231922545455)),
        "O" => array(array(276.0783333333333, 314.61680214), array(53.02166666666667, 314.61680214), array(36.56666666666666, 341.54231922545455), array(292.5333333333333, 341.54231922545455)),
        "ND" => array(array(135.29666666666668, 179.98921664363638), array(53.02166666666667, 314.61680214), array(221.22833333333332, 314.61680214)),
    ];


    public function getResult()
    {
        $ppmTotal = $this->totalPpm($this->ethane, $this->hydrogen, $this->methane);
        $proc1 = $this->firstProc($this->hydrogen, $ppmTotal);
        $proc2 = $this->secondProc($this->ethane, $ppmTotal);
        $proc3 = $this->thirdProc($this->methane, $ppmTotal);
        $check = $this->normalCheck($this->hydrogen_roc, $this->ethane_roc, $this->methane_roc, $this->hydrogen, $this->ethane, $this->methane);
        $code =  $this->duvalT3code($check, $proc1, $proc2, $proc3);

        return $code;
    }

    private function duvalT3Code($check, $proc1, $proc2, $proc3)
    {
        // THIS HAS BEEN CHANGE VIA REQUEST OF SIR JERICHO LASAM -- 19-JAN-2024
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 >= 2 && $proc3 <= 15 && $proc2 <= 1 && $proc2 > 0) {
        //     return "PD";
        // } else if (($proc2 < 46 && $proc2 > 0 && $proc3 < 36) || ($proc2 < 46 && $proc2 > 24 && $proc1 > 15 && $proc3 > 36) || ($proc2 < 46 && $proc2 > 30 && $proc1 < 15 && $proc1 > 9)) {
        //     return "S";
        // } else if (($proc2 > 0 && $proc2 < 30 && $proc1 < 15 && $proc1 > 0) || ($proc1 > 15 && $proc3 > 36 && $proc2 > 0 && $proc2 < 24)) {
        //     return "C";
        // } else if ($proc1 <= 9 && $proc2 > 30) {
        //     return "O";
        // } else {
        //     return "ND";
        // }

        // if ($check == 1) {
        //     return "Normal";
        // } else if ($proc3 > 2 && $proc3 <= 15 && $proc2 <= 1) {
        //     return "PD";
        // } else if (($proc3 > 36 && $proc2 <= 24) || ($proc2 <= 30 &&  $proc1 <= 15)) {
        //     return "C";
        // } else if ($proc1 <= 9) {
        //     return "O";
        // } else if ($proc2 > 46) {
        //     return "ND";
        // } else {
        //     return "S";
        // }
        if ($check == 1){
            return "Normal";
        }
        $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
        $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
        $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
        $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

        $ch4_pc = $proc3;
        $c2h6_pc = $proc2;
        $h2_pc = $proc1;

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
          if ($helper->pointInPolygon($point, $this->regions['PD'])) {
            return "PD";
        } elseif ($helper->pointInPolygon($point, $this->regions['S'])) {
            return "S";
        } elseif ($helper->pointInPolygon($point, $this->regions['C'])) {
            return "C";
        } elseif ($helper->pointInPolygon($point, $this->regions['O'])) {
            return "O";
        } elseif ($helper->pointInPolygon($point, $this->regions['ND'])) {
            return "ND";
        } else {
            return "N/A";
        }
    }

    private function normalCheck($ethane_roc, $hydrogen_roc, $methane_roc, $ethane, $hydrogen, $methane)
    {
        $hydrogenlowerlimit = 119;
        $ethanelowerlimit = 111;
        $methanelowerlimit = 85;
        $hydrogenroclowerlimit = 1.8;
        $ethaneroclowerlimit = 1.2;
        $methaneroclowerlimit = 1.3;

        if ($hydrogen_roc < $hydrogenroclowerlimit && $ethane_roc < $ethaneroclowerlimit  && $methane_roc < $methaneroclowerlimit && $hydrogen < $hydrogenlowerlimit && $ethane < $ethanelowerlimit  && $methane < $methanelowerlimit) {
            return 1;
        } else {
            return 0;
        }
    }

    // private function normalCheck($ethane,$hydrogen,$methane){
    //     $hydrogenUpperlimit = 200;
    //     $ethaneUpperlimit = 210;
    //     $methaneUpperlimit = 135;

    //     if ($hydrogen < $hydrogenUpperlimit && $ethane < $ethaneUpperlimit  && $methane < $methaneUpperlimit){
    //         return "1";
    //     }
    //     else{
    //         return "0";
    //     }
    // }

    private function firstProc($hydrogen, $ppmTotal)
    {
        $result = 100 * $hydrogen / $ppmTotal;
        return $result;
    }
    private function secondProc($ethane, $ppmTotal)
    {
        $result = 100 * $ethane / $ppmTotal;
        return $result;
    }
    private function thirdProc($methane, $ppmTotal)
    {
        $result = 100 * $methane / $ppmTotal;
        return $result;
    }

    private function totalPpm($ethane, $hydrogen, $methane)
    {
        $computation = $ethane + $hydrogen + $methane;
        if ($computation == 0) {
            return .00000000001;
        } else {
            return $computation;
        }
    }
}
