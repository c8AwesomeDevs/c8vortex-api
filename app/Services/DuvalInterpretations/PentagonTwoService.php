<?php

namespace App\Services\DuvalInterpretations;

use App\Contracts\DuvalInterpretationInterface;
use App\Services\DuvalInterpretationService;
use App\Services\SVGgeneration\SVGHelpers;

class PentagonTwoService extends DuvalInterpretationService implements DuvalInterpretationInterface
{

    private $regions = [
        "C" => array(array(123.33333333333333, 468), array(193.33333333333334, 312), array(242.66666666666666, 279.50000000000006), array(283.3333333333333, 468)),
        "T3-H" => array(array(426.6666666666667, 454.99999999999994), array(424, 468), array(283.3333333333333, 468), array(243.33333333333334, 279.50000000000006), array(266.66666666666663, 279.50000000000006)),
        "D2" => array(array(293.3333333333333, 156), array(480, 299), array(426.6666666666667, 454.99999999999994), array(266.66666666666663, 279.50000000000006), array(266.66666666666663, 260)),
        "D1" => array(array(266.66666666666663, 0), array(520, 182.00000000000003), array(480, 299), array(293.3333333333333, 156), array(266.66666666666663, 260)),
        "PD" => array(array(266.66666666666663, 45.49999999999997), array(266.66666666666663, 100.75000000000001), array(260, 100.75000000000001), array(260, 45.49999999999997)),
        "S" => array(array(33.33333333333333, 240.5), array(13.333333333333334, 182.00000000000003), array(266.66666666666663, 0), array(266.66666666666663, 45.49999999999997), array(260, 45.49999999999997), array(260, 100.75000000000001), array(266.66666666666663, 100.75000000000001), array(266.66666666666663, 250.25)),
        "O" => array(array(123.33333333333333, 468), array(109.33333333333331, 468), array(33.33333333333333, 240.5), array(266.66666666666663, 250.25), array(266.66666666666663, 279.50000000000006), array(243.33333333333334, 279.50000000000006), array(193.33333333333334, 312)),
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
        $interpretation = $this->pen2Interpretation($check, $proc1, $proc2, $proc3, $proc4, $proc5);

        return $interpretation;
    }

    private function pen2Interpretation($check, $proc1, $proc2, $proc3, $proc4, $proc5)
    {
        // if ($check == 1) {
        //     return "Normal";
        // } else if ($centroidx >= -1 && $centroidx <= 0 && $centroidy >= 24.5 && $centroidy <= 33) {
        //     return "PD";
        // } else if ($centroidx <= -35 || ($centroidx <= 0 && $centroidy > 16 && $centroidx > -35)) {
        //     return "S";
        // } else if (($centroidx > 0 && $centroidy > 16) || $centroidx > 32) {
        //     return "D1";
        // } else if ($centroidx > -35 && $centroidx < -21.5 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -35 && $centroidx < -21.5 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "O";
        // } else if ($centroidx > -21.5 && $centroidx <= -11 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -21.5 && $centroidx <= -11 && $centroidy > 2.32380952380952 * $centroidx + 17.5619047619048 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "O";
        // } else if ($centroidx > -21.5 && $centroidx <= -11 && $centroidy <= 2.32380952380952 * $centroidx + 17.5619047619048) {
        //     return "C";
        // } else if ($centroidx > -11 && $centroidx <= -6 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -11 && $centroidx <= -6 && $centroidy > 0.8 * $centroidx + 0.800000000000001 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "O";
        // } else if ($centroidx > -11 && $centroidx <= -6 && $centroidy <= 0.8 * $centroidx + 0.800000000000001) {
        //     return "C";
        // } else if ($centroidx > -6 && $centroidx <= -3.5 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -6 && $centroidx <= -3.5 && $centroidy > 0.2 * $centroidx - 2.8 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "O";
        // } else if ($centroidx > -6 && $centroidx <= -3.5 && $centroidy > 0.2 * $centroidx - 2.8) {
        //     return "C";
        // } else if ($centroidx > -3.5 && $centroidx <= 0 && $centroidy > -0.0457142857142857 * $centroidx + 1.5) {
        //     return "S";
        // } else if ($centroidx > -3.5 && $centroidx <= 0 && $centroidy > 0.142857142857143 * $centroidx - 3 && $centroidy <= -0.0457142857142857 * $centroidx + 1.5) {
        //     return "O";
        // } else if ($centroidx > -3.5 && $centroidx <= 0 && $centroidy > -4.81666666666667 * $centroidx - 20.3583333333333 && $centroidy <= 0.142857142857143 * $centroidx - 3) {
        //     return "T3-H";
        // } else if ($centroidx > -3.5 && $centroidx <= 0 && $centroidy <= -4.81666666666667 * $centroidx - 20.3583333333333) {
        //     return "C";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy > 3.625 * $centroidx + 1.5) {
        //     return "D1";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy > -1.11111111111111 * $centroidx - 3 && $centroidy <= 3.625 * $centroidx + 1.5) {
        //     return "D2";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy > -4.81666666666667 * $centroidx - 20.3583333333333 && $centroidy <= 1.11111111111111 * $centroidx - 3) {
        //     return "T3-H";
        // } else if ($centroidx > 0 && $centroidx <= 1 && $centroidy <= -4.81666666666667 * $centroidx - 20.3583333333333) {
        //     return "C";
        // } else if ($centroidx > 1 && $centroidx <= 4 && $centroidy > 3.625 * $centroidx + 1.5) {
        //     return "D1";
        // } else if ($centroidx > 1 && $centroidx <= 4 && $centroidy > -1.11111111111111 * $centroidx - 3 && $centroidy <= 3.625 * $centroidx + 1.5) {
        //     return "D2";
        // } else if ($centroidx > 1 && $centroidx <= 4 && $centroidy <= -1.11111111111111 * $centroidx - 3) {
        //     return "T3-H";
        // } else if ($centroidx > 4 && $centroidx <= 32 && $centroidy > -0.789285714285714 * $centroidx + 19.1571428571429) {
        //     return "D1";
        // } else if ($centroidx > 4 && $centroidx <= 32 && $centroidy > -1.11111111111111 * $centroidx - 3 && $centroidy <= -0.789285714285714 * $centroidx + 19.1571428571429) {
        //     return "D2";
        // } else if ($centroidx > 4 && $centroidx <= 32 && $centroidy <= -1.11111111111111 * $centroidx - 3) {
        //     return "T3-H";
        // } else {
        //     return "ND";
        // }

        if($check == 1){
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
        // before the points can be plotted the coordinates should be scalled/translated 
        // to the proper quadrant and domain where the triangle is located in the plane
        $x = $helper->scaling_x($dt_x, 'pentagon');
        $y = $helper->scaling_y($dt_y, 'pentagon');

        $point = array($x, $y);

         // Check if the point is inside each polygon
         if ($helper->pointInPolygon($point, $this->regions['C'])) {
            return "C";
        } elseif ($helper->pointInPolygon($point, $this->regions['T3-H'])) {
            return "T3-H";
        } elseif ($helper->pointInPolygon($point, $this->regions['D2'])) {
            return "D2";
        } elseif ($helper->pointInPolygon($point, $this->regions['D1'])) {
            return "D1";
        } elseif ($helper->pointInPolygon($point, $this->regions['PD'])) {
            return "PD";
        } elseif ($helper->pointInPolygon($point, $this->regions['S'])) {
            return "S";
        } elseif ($helper->pointInPolygon($point, $this->regions['O'])) {
            return "O";
        } else {
            return "N/A";
        }
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

        if ($hydrogen_roc < $hydrogenroclowerlimit && $ethane_roc < $ethaneroclowerlimit  && $methane_roc < $methaneroclowerlimit && $ethylene_roc < $ethyleneroclowerlimit && $acetylene_roc < $acetyleneroclowerlimit &&  $hydrogen < $hydrogenlowerlimit && $ethane < $ethanelowerlimit  && $methane < $methanelowerlimit && $ethylene < $ethylenelowerlimit && $acetylene < $acetylenelowerlimit) {
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
