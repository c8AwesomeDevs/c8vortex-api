<?php

namespace App\Services\SVGgeneration;

use App\Services\SVGgeneration\SVGHelpers;

class T7SVGService extends SVGHelpers
{
    // private $regions = [
    //     "S" => array(array(219.4, 42.3699070072201), array(237.68333333333334, 72.2871482288644), array(122.49833333333333, 260.76576792522314), array(104.215, 230.84852670357895)),
    //     "O" => array(array(237.68333333333334, 72.2871482288644), array(182.83333333333334, 162.03887189379725), array(228.54166666666669, 236.8319749479078), array(283.3916666666667, 147.0802512829751)),
    //     "C" => array(array(283.3916666666667, 147.0802512829751), array(402.23333333333335, 341.5423192236627), array(164.55, 341.5423192236627)),
    //     "T3" => array(array(104.215, 230.84852670357895), array(129.81166666666667, 272.73266441388085), array(87.76, 341.5423192236627), array(36.56666666666666, 341.5423192236627)),
    //     "ND" => array(array(182.83333333333334, 162.03887189379725), array(228.54166666666669, 236.8319749479078), array(164.55, 341.5423192236627), array(87.76, 341.5423192236627), array(129.81166666666667, 272.73266441388085), array(122.49833333333333, 260.76576792522314)),
    // ];

    // private function getDuvalsCode($point)
    // {
    //     // Check if the point is inside each polygon
    //     if ($this->pointInPolygon($point, $this->regions['S'])) {
    //         return "S";
    //     } elseif ($this->pointInPolygon($point, $this->regions['O'])) {
    //         return "O";
    //     } elseif ($this->pointInPolygon($point, $this->regions['C'])) {
    //         return "C";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T3'])) {
    //         return "T3";
    //     } elseif ($this->pointInPolygon($point, $this->regions['ND'])) {
    //         return "ND";
    //     } else {
    //         return "N/A";
    //     }
    // }

    public function gen_t7($data)
    {
        // do not plot data with 'Normal' interpretation
        // we use array_filter to only get what we want
        // we use array_values to reset the indexes back to 0
        $clean = array_values(array_filter($data, function ($v, $k) {
            return $v['t7'] != 'Normal';
        }, ARRAY_FILTER_USE_BOTH));

        // these are the zones/regions of the triangle
        $polygons = "<polygon points='219.4,42.3699070072201 237.68333333333334,72.2871482288644 122.49833333333333,260.76576792522314 104.215,230.84852670357895' fill='orange' stroke-width='2'/><polygon points='237.68333333333334,72.2871482288644 182.83333333333334,162.03887189379725 228.54166666666669,236.8319749479078 283.3916666666667,147.0802512829751' fill='LimeGreen' stroke-width='2'/><polygon points='283.3916666666667,147.0802512829751 402.23333333333335,341.5423192236627 164.55,341.5423192236627' fill='HotPink' stroke-width='2'/><polygon points='104.215,230.84852670357895 129.81166666666667,272.73266441388085 87.76,341.5423192236627 36.56666666666666,341.5423192236627' fill='blue' stroke-width='2'/><polygon points='182.83333333333334,162.03887189379725 228.54166666666669,236.8319749479078 164.55,341.5423192236627 87.76,341.5423192236627 129.81166666666667,272.73266441388085 122.49833333333333,260.76576792522314' fill='SkyBlue' stroke-width='2'/>";
        // these are the texts/labels outside the triangle
        $texts = "<text x='320' y='-190' transform='rotate(60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Ethylene (C₂H₄)</text><text x='215' y='365' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Ethane (C₂H₆)</text><text x='-105' y='191' transform='rotate(-60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Methane (CH₄)</text>";

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
            $dt1_total = $value['c2h4'] + $value['c2h6'] + $value['ch4']; //Getting the percentage of each value
            $c2h4_pc = ($value['c2h4'] / $dt1_total) * 100;
            $c2h6_pc = ($value['c2h6'] / $dt1_total) * 100;
            $ch4_pc = ($value['ch4'] / $dt1_total) * 100;

            // determining the x and y coordinates of the points
            $dt1_x = ($c2h4_pc - $c2h6_pc) * sin(30 * pi() / 180);
            $dt1_y = ($ch4_pc / 100) * $tr_h + $tr_ymin;
            // before the points can be plotted the coordinates should be scalled/translated 
            // to the proper quadrant and domain where the triangle is located in the plane
            $x = $this->scaling_x($dt1_x);
            $y = $this->scaling_y($dt1_y);
            // plot the scalled coordinates
            $circles .= "<circle r='{$this->circleSize($key)}' cx='{$x}' cy='{$y}' id='plot-t7-{$key}' style='fill: {$this->circleColor($key,$clean_length)}; cursor: pointer;'/>";

            // creating connectinglines
            if (count($coordinate_buffer) == 1) {
                $immediate_history = array_pop($coordinate_buffer); // pop the buffer

                // make the line using the coordinates from the buffer and the lastest coordinates
                // $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='#ffaa00' stroke-width='{$this->LINE_THICKNESS}'/>";
                $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='{$this->LINE_COLOR}' stroke-width='{$this->LINE_THICKNESS}'/>";
            }
            array_push($coordinate_buffer, ["x" => $x, "y" => $y]); // push to the buffer even if count($coordinate_buffer) is 1 or 0

            // genrate tooltip html
            $tooltipHTML .= "<div id='plot-t7-{$key}-pup' style='padding: 5px; font-size: 10px;background: #fff; position: absolute; display: none; visibility: hidden; font-family: calibri; border: 1px solid #cccccc'><table width='100%' class='basic-table' style='font-size:12px;'>";
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Timestamp</td><td>{$value['timestamp']}</td></tr>";
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Ethylene (C₂H₄) </td><td>%s (%s%%)</td></tr>",
                number_format($value['c2h4'], 2),
                number_format($c2h4_pc, 2)
            );
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
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault </td><td>{$value['t7']}</td></tr>";
            // $code = $this->getDuvalsCode(array($x, $y));
            // $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault* </td><td>{$code}</td></tr>";
            $tooltipHTML .= "</table></div>";

            // add to the popup mapping
            array_push($popup_mapping, ["plot-t7-{$key}", "plot-t7-{$key}-pup"]);
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
