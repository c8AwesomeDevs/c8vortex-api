<?php

namespace App\Services\SVGgeneration;

use App\Services\SVGgeneration\SVGHelpers;

class T6SVGService extends SVGHelpers
{
    // private $regions = [
    //     "S" => array(array(219.4, 42.36990700363636), array(241.34000000000003, 78.27059648545453), array(80.44666666666666, 341.54231922545455), array(36.56666666666666, 341.54231922545455)),
    //     "C" => array(array(285.22, 150.07197541454545), array(241.34000000000003, 221.87335433672726), array(314.47333333333336, 341.54231922545455), array(402.23333333333335, 341.54231922545455)),
    //     "O" => array(array(298.0183333333334, 314.61680214), array(314.47333333333336, 341.54231922545455), array(80.44666666666666, 341.54231922545455), array(96.90166666666667, 314.61680214)),
    //     "ND" => array(array(241.34000000000003, 78.27059648545453), array(96.90166666666667, 314.61680214), array(298.0183333333334, 314.61680214), array(241.34000000000003, 221.87335433672726), array(285.22, 150.07197541454545)),
    //     "PD" => array(array(223.05666666666667, 48.353355256363685), array(221.22833333333332, 51.345079365454524), array(244.99666666666667, 90.23749295636362), array(246.82500000000002, 87.24576884727274)),
    // ];

    // private function getDuvalsCode($point)
    // {
    //     // Check if the point is inside each polygon
    //     if ($this->pointInPolygon($point, $this->regions['S'])) {
    //         return "S";
    //     } elseif ($this->pointInPolygon($point, $this->regions['C'])) {
    //         return "C";
    //     } elseif ($this->pointInPolygon($point, $this->regions['O'])) {
    //         return "O";
    //     } elseif ($this->pointInPolygon($point, $this->regions['ND'])) {
    //         return "ND";
    //     } elseif ($this->pointInPolygon($point, $this->regions['PD'])) {
    //         return "PD";
    //     } else {
    //         return "N/A";
    //     }
    // }

    public function gen_t6($data)
    {
        // do not plot data with 'Normal' interpretation
        // we use array_filter to only get what we want
        // we use array_values to reset the indexes back to 0
        $clean = array_values(array_filter($data, function ($v, $k) {
            return $v['t6'] != 'Normal';
        }, ARRAY_FILTER_USE_BOTH));

        // these are the zones/regions of the triangle
        $polygons = "<polygon points='219.4,42.36990700363636 241.34000000000003,78.27059648545453 80.44666666666666,341.54231922545455 36.56666666666666,341.54231922545455' fill='hotpink' stroke-width='2'/><polygon points='285.22,150.07197541454545 241.34000000000003,221.87335433672726 314.47333333333336,341.54231922545455 402.23333333333335,341.54231922545455' fill='LimeGreen' stroke-width='2'/><polygon points='298.0183333333334,314.61680214 314.47333333333336,341.54231922545455 80.44666666666666,341.54231922545455 96.90166666666667,314.61680214' fill='blue' stroke-width='2'/><polygon points='241.34000000000003,78.27059648545453 96.90166666666667,314.61680214 298.0183333333334,314.61680214 241.34000000000003,221.87335433672726 285.22,150.07197541454545' fill='SkyBlue' stroke-width='2'/><polygon points='223.05666666666667,48.353355256363685 221.22833333333332,51.345079365454524 244.99666666666667,90.23749295636362 246.82500000000002,87.24576884727274' fill='pink' stroke-width='2'/>";
        // these are the texts/labels outside the triangle
        $texts = "<text x='320' y='-190' transform='rotate(60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Methane (CH₄)</text><text x='215' y='365' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Ethane (C₂H₆)</text><text x='-105' y='191' transform='rotate(-60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Dihydrogen (H₂)</text>";

        // setting the configs of the triangle; these will used to plot the points inside the triangle
        $tr_l = 100; //Equilateral triangle side length = 100, Origin at 33.33% of length of each side
        $tr_h = sqrt(pow($tr_l, 2) - pow($tr_l / 2, 2)); //86.60254038
        $tr_ymax = ($tr_l / 2) / cos((30 / 180) * pi()); //57.73502692
        $tr_ymin = $tr_ymax - $tr_h;  //-28.86751346

        $circles = ""; // this is the string that will hold all the generated circles/points from the loop
        $connectingLines = ""; // this is the string that will hold all the generated connectingLines from the loop
        $tooltipHTML = ""; // this is the string that will hold all the generated table rows for the popup from the loop
        $coordinate_buffer = [];
        $clean_length = count($clean);
        $popup_mapping = [];
        foreach ($clean as $key => $value) {
            $dt1_total = $value['ch4'] + $value['c2h6'] + $value['h2']; //Getting the percentage of each value
            $ch4_pc = ($value['ch4'] / $dt1_total) * 100;
            $c2h6_pc = ($value['c2h6'] / $dt1_total) * 100;
            $h2_pc = ($value['h2'] / $dt1_total) * 100;

            // determining the x and y coordinates of the points
            $dt1_x = ($ch4_pc - $c2h6_pc) * sin(30 * pi() / 180);
            $dt1_y = ($h2_pc / 100) * $tr_h + $tr_ymin;
            // before the points can be plotted the coordinates should be scalled/translated 
            // to the proper quadrant and domain where the triangle is located in the plane
            $x = $this->scaling_x($dt1_x);
            $y = $this->scaling_y($dt1_y);
            // plot the scalled coordinates
            $circles .= "<circle r='{$this->circleSize($key)}' cx='{$x}' cy='{$y}' id='plot-t6-{$key}' style='fill: {$this->circleColor($key,$clean_length)}; cursor: pointer;'/>";

            // creating connectinglines
            if (count($coordinate_buffer) == 1) {
                $immediate_history = array_pop($coordinate_buffer); // pop the buffer

                // make the line using the coordinates from the buffer and the lastest coordinates
                // $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='#ffaa00' stroke-width='{$this->LINE_THICKNESS}'/>";
                $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='{$this->LINE_COLOR}' stroke-width='{$this->LINE_THICKNESS}'/>";
            }
            array_push($coordinate_buffer, ["x" => $x, "y" => $y]); // push to the buffer even if count($coordinate_buffer) is 1 or 0

            // genrate tooltip html
            $tooltipHTML .= "<div id='plot-t6-{$key}-pup' style='padding: 5px; font-size: 10px;background: #fff; position: absolute; display: none; visibility: hidden; font-family: calibri; border: 1px solid #cccccc'><table width='100%' class='basic-table' style='font-size:12px;'>";
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Timestamp</td><td>{$value['timestamp']}</td></tr>";
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Methane (CH₄) </td><td>%s (%s%%)</td></tr>",
                number_format($value['ch4'], 2),
                number_format($ch4_pc, 2)
            );
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
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault </td><td>{$value['t6']}</td></tr>";
            // $code = $this->getDuvalsCode(array($x, $y));
            // $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault* </td><td>{$code}</td></tr>";
            $tooltipHTML .= "</table></div>";

            // add to the popup mapping
            array_push($popup_mapping, ["plot-t6-{$key}", "plot-t6-{$key}-pup"]);
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
