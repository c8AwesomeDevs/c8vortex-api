<?php

namespace App\Services\SVGgeneration;

use App\Services\SVGgeneration\SVGHelpers;

class P1SVGService extends SVGHelpers
{
    // private $regions = [
    //     "D1" => array(array(266.66666666666663, 0), array(520, 182.00000000000003), array(480, 299), array(293.3333333333333, 156), array(266.66666666666663, 260)),
    //     "PD" => array(array(266.66666666666663, 45.49999999999997), array(266.66666666666663, 100.75000000000001), array(260, 100.75000000000001), array(260, 45.49999999999997)),
    //     "S" => array(array(33.33333333333333, 240.5), array(13.333333333333334, 182.00000000000003), array(266.66666666666663, 0), array(266.66666666666663, 45.49999999999997), array(260, 45.49999999999997), array(260, 100.75000000000001), array(266.66666666666663, 100.75000000000001), array(266.66666666666663, 250.25)),
    //     "D2" => array(array(260, 273), array(293.3333333333333, 156), array(480, 299), array(426.6666666666667, 454.99999999999994)),
    //     "T3" => array(array(426.6666666666667, 454.99999999999994), array(421.99999999999994, 469.95), array(273.3333333333333, 469.95), array(226.66666666666666, 285.99999999999994), array(260, 266.5)),
    //     "T2" => array(array(226.66666666666666, 285.99999999999994), array(273.3333333333333, 469.95), array(120, 469.95)),
    //     // there is overlapping happening here idk why; T1 is overlapped with T2, T3, and D2
    //     "T1" => array(array(113.33333333333333, 469.95), array(273.3333333333333, 469.95), array(120, 469.95), array(226.66666666666666, 285.99999999999994), array(267.1333333333333, 273), array(266.66666666666663, 250.25), array(33.33333333333333, 240.5)),
    // ];

    // private function getDuvalsCode($point)
    // {
    //     // Check if the point is inside each polygon
    //     if ($this->pointInPolygon($point, $this->regions['D1'])) {
    //         return "D1";
    //     } elseif ($this->pointInPolygon($point, $this->regions['PD'])) {
    //         return "PD";
    //     } elseif ($this->pointInPolygon($point, $this->regions['S'])) {
    //         return "S";
    //     } elseif ($this->pointInPolygon($point, $this->regions['D2'])) {
    //         return "D2";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T3'])) {
    //         return "T3";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T2'])) {
    //         return "T2";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T1'])) {
    //         return "T1";
    //     } else {
    //         return "N/A";
    //     }
    // }

    public function gen_p1($data)
    {
        // do not plot data with 'Normal' interpretation
        // we use array_filter to only get what we want
        // we use array_values to reset the indexes back to 0
        $clean = array_values(array_filter($data, function ($v, $k) {
            return $v['p1'] != 'Normal';
        }, ARRAY_FILTER_USE_BOTH));

        // these are the zones/regions of the triangle
        $polygons = "<polygon points='266.66666666666663,0 520,182.00000000000003 480,299 293.3333333333333,156 266.66666666666663,260' fill='LimeGreen' stroke-width='2'/><polygon points='266.66666666666663,45.49999999999997 266.66666666666663,100.75000000000001 260,100.75000000000001 260,45.49999999999997' fill='HotPink' stroke-width='2'/><polygon points='33.33333333333333,240.5 13.333333333333334,182.00000000000003 266.66666666666663,0 266.66666666666663,45.49999999999997 260,45.49999999999997 260,100.75000000000001 266.66666666666663,100.75000000000001 266.66666666666663,250.25' fill='blue' stroke-width='2'/><polygon points='260,273 293.3333333333333,156 480,299 426.6666666666667,454.99999999999994' fill='#679A00' stroke-width='2'/><polygon points='426.6666666666667,454.99999999999994 421.99999999999994,469.95 273.3333333333333,469.95 226.66666666666666,285.99999999999994 260,266.5' fill='pink' stroke-width='2'/><polygon points='226.66666666666666,285.99999999999994 273.3333333333333,469.95 120,469.95' fill='purple' stroke-width='2'/><polygon points='113.33333333333333,469.95 273.3333333333333,469.95 120,469.95 226.66666666666666,285.99999999999994 267.1333333333333,273 266.66666666666663,250.25 33.33333333333333,240.5' fill='SkyBlue' stroke-width='2'/>";
        // these are the texts/labels outside the triangle
        $texts = "<text x='48' y='152' transform='rotate(-39.4)' text-anchor='middle' style='font-family: calibri; font-size: 12px; fill: green; stroke-width: 0px;'>% Ethane (C₂H₆)</text><text x='360' y='-151' transform='rotate(33.75)' text-anchor='middle' style='font-size: 12px; font-family: calibri; fill: green; stroke-width: 0px;'>% Dihydrogen (H₂)</text><text x='-160' y='555' transform='rotate(-75)' text-anchor='middle' style='font-size: 10px; font-family: calibri; fill: green; stroke-width: 0px;'>% Acetylene (C₂H₂)</text><text x='255' y='487' text-anchor='middle' style='font-size: 12px; font-family: calibri; fill: green; stroke-width: 0px;'>% Ethylene (C₂H₄)</text><text x='310' y='65' transform='rotate(70)' text-anchor='middle' style='font-size: 10px; font-family: calibri; fill: green; stroke-width: 0px;'>% Methane (CH₄)</text>";

        // setting the configs of the triangle; these will used to plot the points inside the triangle
        // $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
        // $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
        // $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
        // $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

        $circles = ""; // this is the string that will hold all the generated circles/points from the loop
        $connectingLines = ""; // this is the string that will hold all the generated connectingLines from the loop
        $tooltipHTML = ""; // this is the string that will hold all the generated table rows for the popup from the loop
        $coordinate_buffer = [];
        $clean_length = count($clean);
        $popup_mapping = [];
        foreach ($clean as $key => $value) {
            $tot_gas = $value['c2h6'] + $value['h2'] + $value['c2h2'] + $value['c2h4'] + $value['ch4']; //Getting the percentage of each value
            $c2h6_pc = ($value['c2h6'] / $tot_gas) * 100;
            $h2_pc = ($value['h2'] / $tot_gas) * 100;
            $c2h2_pc = ($value['c2h2'] / $tot_gas) * 100;
            $c2h4_pc = ($value['c2h4'] / $tot_gas) * 100;
            $ch4_pc = ($value['ch4'] / $tot_gas) * 100;

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

            // before the points can be plotted the coordinates should be scalled/translated 
            // to the proper quadrant and domain where the triangle is located in the plane
            $x = $this->scaling_x($dt_x, 'pentagon');
            $y = $this->scaling_y($dt_y, 'pentagon');
            // plot the scalled coordinates
            $circles .= "<circle r='{$this->circleSize($key)}' cx='{$x}' cy='{$y}' id='plot-p1-{$key}' style='fill: {$this->circleColor($key,$clean_length)}; cursor: pointer;'/>";

            // creating connectinglines
            if (count($coordinate_buffer) == 1) {
                $immediate_history = array_pop($coordinate_buffer); // pop the buffer

                // make the line using the coordinates from the buffer and the lastest coordinates
                // $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='#ffaa00' stroke-width='{$this->LINE_THICKNESS}'/>";
                $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='{$this->LINE_COLOR}' stroke-width='{$this->LINE_THICKNESS}'/>";
            }
            array_push($coordinate_buffer, ["x" => $x, "y" => $y]); // push to the buffer even if count($coordinate_buffer) is 1 or 0

            // genrate tooltip html
            $tooltipHTML .= "<div id='plot-p1-{$key}-pup' style='padding: 5px; font-size: 10px;background: #fff; position: absolute; display: none; visibility: hidden; font-family: calibri; border: 1px solid #cccccc'><table width='100%' class='basic-table' style='font-size:12px;'>";
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Timestamp</td><td>{$value['timestamp']}</td></tr>";
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Ethane (C₂H₆) </td><td>%s (%s%%)</td></tr>",
                number_format($value['c2h6'], 2),
                number_format($c2h6_pc, 2)
            );
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Dihydrogen (H₂) </td><td>%s (%s%%)</td></tr>",
                number_format($value['h2'], 2),
                number_format($h2_pc, 2)
            );
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Ethylene (C₂H₄) </td><td>%s (%s%%)</td></tr>",
                number_format($value['c2h4'], 2),
                number_format($c2h4_pc, 2)
            );
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Acetylene (C₂H₂) </td><td>%s (%s%%)</td></tr>",
                number_format($value['c2h2'], 2),
                number_format($c2h2_pc, 2)
            );
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Methane (CH₄) </td><td>%s (%s%%)</td></tr>",
                number_format($value['ch4'], 2),
                number_format($ch4_pc, 2)
            );
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault </td><td>{$value['p1']}</td></tr>";
            // $code = $this->getDuvalsCode(array($x, $y));
            // $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault* </td><td>{$code}</td></tr>";
            $tooltipHTML .= "</table></div>";

            // add to the popup mapping
            array_push($popup_mapping, ["plot-p1-{$key}", "plot-p1-{$key}-pup"]);
        }

        return [
            "polygons" => $polygons,
            "texts" => $texts,
            "circles" => $circles,
            "connectingLines" => $connectingLines,
            "tooltipHTML" => $tooltipHTML,
            "popup_mapping" => $popup_mapping
        ];
    }
}
