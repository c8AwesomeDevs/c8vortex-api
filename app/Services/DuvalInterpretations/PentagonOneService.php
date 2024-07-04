<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class PentagonOneService extends DuvalInterpretationService implements DuvalInterpretationInterface
{

    private $regions = [
        "D1" => array(array(266.66666666666663, 0), array(520, 182.00000000000003), array(480, 299), array(293.3333333333333, 156), array(266.66666666666663, 260)),
        "PD" => array(array(266.66666666666663, 45.49999999999997), array(266.66666666666663, 100.75000000000001), array(260, 100.75000000000001), array(260, 45.49999999999997)),
        "S" => array(array(33.33333333333333, 240.5), array(13.333333333333334, 182.00000000000003), array(266.66666666666663, 0), array(266.66666666666663, 45.49999999999997), array(260, 45.49999999999997), array(260, 100.75000000000001), array(266.66666666666663, 100.75000000000001), array(266.66666666666663, 250.25)),
        "D2" => array(array(260, 273), array(293.3333333333333, 156), array(480, 299), array(426.6666666666667, 454.99999999999994)),
        "T3" => array(array(426.6666666666667, 454.99999999999994), array(421.99999999999994, 469.95), array(273.3333333333333, 469.95), array(226.66666666666666, 285.99999999999994), array(260, 266.5)),
        "T2" => array(array(226.66666666666666, 285.99999999999994), array(273.3333333333333, 469.95), array(120, 469.95)),
        // there is overlapping happening here idk why; T1 is overlapped with T2, T3, and D2
        "T1" => array(array(113.33333333333333, 469.95), array(273.3333333333333, 469.95), array(120, 469.95), array(226.66666666666666, 285.99999999999994), array(267.1333333333333, 273), array(266.66666666666663, 250.25), array(33.33333333333333, 240.5)),
    ];

    public function getResult()
    {
        $total = $this->totalGas($this->hydrogen, $this->acetylene, $this->ethylene, $this->ethane, $this->methane);
        $proc1 = $this->firstProc($this->hydrogen, $total);
        $proc2 = $this->secondProc($this->acetylene, $total);
        $proc3 = $this->thirdProc($this->ethylene, $total);
        $proc4 = $this->fourthProc($this->ethane, $total);
        $proc5 = $this->fifthProc($this->methane, $total);
        // $centroidx = $this->centroidX($proc1, $proc2, $proc3, $proc4, $proc5);
        // $centroidy = $this->centroidY($proc1, $proc2, $proc3, $proc4, $proc5);
        $check = $this->normalCheck($this->hydrogen_roc, $this->acetylene_roc, $this->ethylene_roc, $this->ethane_roc, $this->methane_roc, $this->hydrogen, $this->acetylene, $this->ethylene, $this->ethane, $this->methane);
        $interpretation = $this->pen1Interpretation($check, $proc1, $proc2, $proc3, $proc4, $proc5);

        return $interpretation;
    }

    private function pen1Interpretation($check, $proc1, $proc2, $proc3, $proc4, $proc5)
    {
        if ($check == 1) {
            return "Normal";
        } 

        $c2h6_pc = $proc4;
        $h2_pc = $proc1;
        $c2h2_pc = $proc2;
        $c2h4_pc = $proc3;
        $ch4_pc = $proc5;

        // determining the x and y coordinates of each gas?
        $c2h6_x = cos(18 * pi() / 180) * $c2h6_pc * (-1);
        $c2h6_y = sin(18 * pi() / 180) * $c2h6_pc;
        $h2_x = 0;
        $h2_y = sin(90 * pi() / 180) * $h2_pc;
        $c2h2_x = cos(18 * pi() / 180) * $c2h2_pc;
        $c2h2_y = sin(18 * pi() / 180) * $c2h2_pc;
        $c2h4_x = cos(54 * pi() / 180) * $c2h4_pc;
        $c2h4_y = sin(54 * pi() / 180) * $c2h4_pc * (-1);
        $ch4_x = cos(54 * pi() / 180) * $ch4_pc * (-1);
        $ch4_y = sin(54 * pi() / 180) * $ch4_pc * (-1);

        $a1 = ($c2h6_x * $h2_y) - ($h2_x * $c2h6_y);
        $a2 = ($h2_x * $c2h2_y) - ($c2h2_x * $h2_y);
        $a3 = ($c2h2_x * $c2h4_y) - ($c2h4_x * $c2h2_y);
        $a4 = ($c2h4_x * $ch4_y) - ($ch4_x * $c2h4_y);
        $a5 = ($ch4_x * $c2h6_y) - ($c2h6_x * $ch4_y);
        $a = ($a1 + $a2 + $a3 + $a4 + $a5) / 2;

        $cx1 = ($c2h6_x + $h2_x) * $a1;
        $cx2 = ($h2_x + $c2h2_x) * $a2;
        $cx3 = ($c2h2_x + $c2h4_x) * $a3;
        $cx4 = ($c2h4_x + $ch4_x) * $a4;
        $cx5 = ($ch4_x + $c2h6_x) * $a5;

        $cy1 = ($c2h6_y + $h2_y) * $a1;
        $cy2 = ($h2_y + $c2h2_y) * $a2;
        $cy3 = ($c2h2_y + $c2h4_y) * $a3;
        $cy4 = ($c2h4_y + $ch4_y) * $a4;
        $cy5 = ($ch4_y + $c2h6_y) * $a5;

        $dt_x = ($cx1 + $cx2 + $cx3 + $cx4 + $cx5) / (6 * $a);
        $dt_y = ($cy1 + $cy2 + $cy3 + $cy4 + $cy5) / (6 * $a);
        
        $helper = new SVGHelpers;
        $x = $helper->scaling_x($dt_x, 'pentagon');
        $y = $helper->scaling_y($dt_y, 'pentagon');

        $point = array($x, $y);

        // Check if the point is inside each polygon
        if ($helper->pointInPolygon($point, $this->regions['D1'])) {
            return "D1";
        } elseif ($helper->pointInPolygon($point, $this->regions['PD'])) {
            return "PD";
        } elseif ($helper->pointInPolygon($point, $this->regions['S'])) {
            return "S";
        } elseif ($helper->pointInPolygon($point, $this->regions['D2'])) {
            return "D2";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3'])) {
            return "T3";
        } elseif ($helper->pointInPolygon($point, $this->regions['T2'])) {
            return "T2";
        } elseif ($helper->pointInPolygon($point, $this->regions['T1'])) {
            return "T1";
        } else {
            return "N/A";
        }
        // if ($check == 1) {
        //     return "Normal";
        // } else 
        // if ($centroidx >= -1 && $centroidx <= 0 && $centroidy >= 24.5 && $centroidy <= 33) {
        //     return "PD";
        // } else if ($centroidx <= -35 || ($centroidx <= 0 && $centroidy > 16 && $centroidx > -35)) {
        //     return "S";
        // } else if (($centroidx > 32 && $centroidx > 0) || $centroidy > 16) {
        //     return "D1";
        // } else if ($centroidx > -35 && $centroidx < -22.5 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -35 && $centroidx < -22.5 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "T1";
        // } else if ($centroidx > -22.5 && $centroidx <= -6 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -21.5 && $centroidx <= -6 && $centroidy > 1.72121212121212 * $centroidx + 6.32727272727273 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "T1";
        // } else if ($centroidx > -22.5 && $centroidx <= -6 && $centroidy <= 1.72121212121212 * $centroidx + 6.32727272727273) {
        //     return "T2";
        // } else if ($centroidx > -6 && $centroidx <= 0 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -6 && $centroidx <= 0 && $centroidy > 0.166666666666667 * $centroidx - 3 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "T1";
        // } else if ($centroidx > -6 && $centroidx <= 0 && $centroidy > -4.05714285714286 * $centroidx - 28.3428571428571 && $centroidy <= 0.166666666666667 * $centroidx - 3) {
        //     return "T3";
        // } else if ($centroidx > -6 && $centroidx <= 0 && $centroidy <= -4.05714285714286 * $centroidx - 28.3428571428571) {
        //     return "T2";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy > 3.625 * $centroidx + 1.5) {
        //     return "D1";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy > -1.11111111111111 * $centroidx - 3 && $centroidy <= 3.625 * $centroidx + 1.5) {
        //     return "D2";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy > -4.05714285714286 * $centroidx - 28.3428571428571 && $centroidy <= -1.11111111111111 * $centroidx - 3) {
        //     return "T3";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy <= -4.05714285714286 * $centroidx - 28.3428571428571) {
        //     return "T2";
        // } else if ($centroidx > 1 && $centroidx <= 4 && $centroidy > 3.625 * $centroidx + 1.5) {
        //     return "D1";
        // } else if ($centroidx > 1 && $centroidx <= 4 && $centroidy > -1.11111111111111 * $centroidx - 3 && $centroidy <= 3.625 * $centroidx + 1.5) {
        //     return "D2";
        // } else if ($centroidx > 1 && $centroidx <= 4 && $centroidy <= -1.11111111111111 * $centroidx - 3) {
        //     return "T3";
        // } else if ($centroidx > 4 && $centroidx <= 32 && $centroidy > -0.789285714285714 * $centroidx + 19.1571428571429) {
        //     return "D1";
        // } else if ($centroidx > 4 && $centroidx <= 32 && $centroidy > -1.11111111111111 * $centroidx - 3 && $centroidy <= -0.789285714285714 * $centroidx + 19.1571428571429) {
        //     return "D2";
        // } else if ($centroidx > 4 && $centroidx <= 32 && $centroidy <= -1.11111111111111 * $centroidx - 3) {
        //     return "T3";
        // } else {
        //     return "ND";
        // }
    }

    private function normalCheck($hydrogen_roc, $acetylene_roc, $ethylene_roc, $ethane_roc, $methane_roc, $hydrogen, $acetylene, $ethylene, $ethane, $methane)
    {
        $hydrogenlowerlimit = 119;
        $ethanelowerlimit = 111;
        $acetylenelowerlimit = 5;
        $ethylenelowerlimit = 87;
        $methanelowerlimit = 85;
        $hydrogenroclowerlimit = 1.8;
        $ethaneroclowerlimit = 1.2;
        $acetyleneroclowerlimit = 1;
        $ethyleneroclowerlimit = 1;
        $methaneroclowerlimit = 1.3;

        if ($ethane_roc < $ethaneroclowerlimit  && $methane_roc < $methaneroclowerlimit && $ethylene_roc < $ethyleneroclowerlimit && $acetylene_roc < $acetyleneroclowerlimit && $hydrogen_roc < $hydrogenroclowerlimit && $hydrogen < $hydrogenlowerlimit && $ethane < $ethanelowerlimit  && $methane < $methanelowerlimit && $ethylene < $ethylenelowerlimit && $acetylene < $acetylenelowerlimit) {
            return 1;
        } else {
            return 0;
        }
    }

    private function centroidY($proc1, $proc2, $proc3, $proc4, $proc5)
    {
        // $result = 40 * $proc1 + 12.4 * $proc2 - 32.4 * $proc3 + 12.4 * $proc4 - 32.4 * $proc5;
        $result = 100 * (((((0.30902 * $proc4) + (1 * $proc1)) * (((-0.95106 * $proc4) * (1 * $proc1)) - (0 * (0.30902 * $proc4)))) + (((1 * $proc1) + (0.30902 * $proc2)) * ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * (1 * $proc1)))) + (((0.30902 * $proc2) + (-0.80902 * $proc3)) * (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2)))) + (((-0.80902 * $proc3) + (-0.80902 * $proc5)) * (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3)))) + (((-0.80902 * $proc5) + (0.30902 * $proc4)) * (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))) / (3 * ((((-0.95106 * $proc4) * (1 * $proc1)) - (0 * (0.30902 * $proc4))) + ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * (1 * $proc1))) + (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2))) + (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3))) + (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))));
        return $result;
    }

    private function centroidX($proc1, $proc2, $proc3, $proc4, $proc5)
    {
        // $result = 0 * $proc1 + 38 * $proc2 + 23.5 * $proc3 - 38 * $proc4 - 23.5 * $proc5;
        $result = 100 * (((((-0.95106 * $proc4) + 0) * (((-0.95106 * $proc4) * $proc1) - (0 * (0.30902 * $proc4)))) + ((0 + (0.95106 * $proc2)) * ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * $proc1))) + (((0.95106 * $proc2) + (0.58779 * $proc3)) * (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2)))) + (((0.58779 * $proc3) + (-0.58779 * $proc5)) * (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3)))) + (((-0.58779 * $proc5) + (-0.95106 * $proc4)) * (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))) / (3 * ((((-0.95106 * $proc4) * $proc1) - (0 * (0.30902 * $proc4))) + ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * $proc1)) + (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2))) + (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3))) + (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))));
        return $result;
    }

    private function fifthProc($methane, $total)
    {
        $result = 100 * $methane / $total;
        return $result;
    }

    private function fourthProc($ethane, $total)
    {
        $result = 100 * $ethane / $total;
        return $result;
    }

    private function thirdProc($ethylene, $total)
    {
        $result = 100 * $ethylene / $total;
        return $result;
    }

    private function secondProc($acetylene, $total)
    {
        $result = 100 * $acetylene / $total;
        return $result;
    }

    private function firstProc($hydrogen, $total)
    {
        $result = 100 * $hydrogen / $total;
        return $result;
    }

    private function totalGas($hydrogen, $acetylene, $ethylene, $ethane, $methane)
    {
        $comptotal = $hydrogen + $acetylene + $ethylene + $ethane + $methane;
        if ($comptotal == 0) {
            return .00000000001;
        } else {
            return $comptotal;
        }
    }
}


// (((-0.95106 * $proc4) * (((-0.95106 * $proc4) * $proc1) + ((0.95106 * $proc2) * $proc1))) + ((0.95106 * $proc2)(-0.95106 * $proc2) * $proc1 * ($proc4 - $proc5))) / ((3 * (((-0.95106 * $proc4) * $proc1) + ((0.95106 * $proc2) * $proc1) + ((0.95106 * $proc2)(-0.95106 * $proc2) * $proc1 * $proc4) + ((0.95106 * $proc2)(-0.95106 * $proc2) * $proc1 * $proc5))));
// 100 * (((((-0.95106 * $proc4) + 0) * (((-0.95106 * $proc4) * $proc1) - (0 * (0.30902 * $proc4)))) + ((0 + (0.95106 * $proc2)) * ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * $proc1))) + (((0.95106 * $proc2) + (0.58779 * $proc3)) * (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2)))) + (((0.58779 * $proc3) + (-0.58779 * $proc5)) * (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3)))) + (((-0.58779 * $proc5) + (-0.95106 * $proc4)) * (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))) / (3 * ((((-0.95106 * $proc4) * $proc1) - (0 * (0.30902 * $proc4))) + ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * $proc1)) + (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2))) + (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3))) + (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))));
// 100 * (((((0.30902 * $proc4) + (1 * $proc1)) * (((-0.95106 * $proc4) * (1 * $proc1)) - (0 * (0.30902 * $proc4)))) + (((1 * $proc1) + (0.30902 * $proc2)) * ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * (1 * $proc1)))) + (((0.30902 * $proc2) + (-0.80902 * $proc3)) * (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2)))) + (((-0.80902 * $proc3) + (-0.80902 * $proc5)) * (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3)))) + (((-0.80902 * $proc5) + (0.30902 * $proc4)) * (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))) / (3 * ((((-0.95106 * $proc4) * (1 * $proc1)) - (0 * (0.30902 * $proc4))) + ((0 * (0.30902 * $proc2)) - ((0.95106 * $proc2) * (1 * $proc1))) + (((0.95106 * $proc2) * (-0.80902 * $proc3)) - ((0.58779 * $proc3) * (0.30902 * $proc2))) + (((0.58779 * $proc3) * (-0.80902 * $proc5)) - ((-0.58779 * $proc5) * (-0.80902 * $proc3))) + (((-0.58779 * $proc5) * (0.30902 * $proc4)) - ((-0.95106 * $proc4) * (-0.80902 * $proc5))))));
