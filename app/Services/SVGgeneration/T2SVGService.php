<?php

namespace App\Services\SVGgeneration;

use App\Services\SVGgeneration\SVGHelpers;

class T2SVGService extends SVGHelpers
{
    // these coordinates was extracted from the generated svg
    // private $regions = [
    //     "X1" => array(array(219.4, 42.36990700363636), array(261.45166666666665, 111.17956182363639), array(155.40833333333333, 284.6995609109091), array(71.305, 284.6995609109091)),
    //     "X3" => array(array(234.02666666666667, 156.0554236327273), array(347.3833333333333, 341.54231922545455), array(120.67000000000002, 341.54231922545455)),
    //     "T2" => array(array(261.45166666666665, 111.17956182363639), array(234.02666666666667, 156.0554236327273), array(283.3916666666667, 236.83197494781817), array(310.81666666666666, 191.95611311454547)),
    //     "T3" => array(array(283.3916666666667, 236.83197494781817), array(310.81666666666666, 191.95611311454547), array(402.23333333333335, 341.54231922545455), array(347.3833333333333, 341.54231922545455)),
    //     "D1" => array(array(93.245, 284.6995609109091), array(71.305, 284.6995609109091), array(36.56666666666666, 341.54231922545455), array(120.67000000000002, 341.54231922545455), array(124.32666666666667, 335.5588709727273), array(62.163333333333334, 335.5588709727273)),
    //     "N" => array(array(155.40833333333333, 284.6995609109091), array(93.245, 284.6995609109091), array(62.163333333333334, 335.5588709727273), array(124.32666666666667, 335.5588709727273)),
    // ];

    // private function getDuvalsCode($point)
    // {
    //     // Check if the point is inside each polygon
    //     if ($this->pointInPolygon($point, $this->regions['X1'])) {
    //         return "X1";
    //     } elseif ($this->pointInPolygon($point, $this->regions['X3'])) {
    //         return "X3";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T2'])) {
    //         return "T2";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T3'])) {
    //         return "T3";
    //     } elseif ($this->pointInPolygon($point, $this->regions['D1'])) {
    //         return "D1";
    //     } elseif ($this->pointInPolygon($point, $this->regions['N'])) {
    //         return "N";
    //     } else {
    //         return "N/A";
    //     }
    // }

    public function gen_t2($data)
    {
        // do not plot data with 'Normal' interpretation
        // we use array_filter to only get what we want
        // we use array_values to reset the indexes back to 0
        $clean = array_values(array_filter($data, function ($v, $k) {
            return $v['t2'] != 'Normal';
        }, ARRAY_FILTER_USE_BOTH));

        // these are the zones/regions of the triangle
        $polygons = "<polygon points='219.4,42.36990700363636 261.45166666666665,111.17956182363639 155.40833333333333,284.6995609109091 71.305,284.6995609109091' fill='pink' stroke-width='2'/><polygon points='234.02666666666667,156.0554236327273 347.3833333333333,341.54231922545455 120.67000000000002,341.54231922545455' fill='purple' stroke-width='2'/><polygon points='261.45166666666665,111.17956182363639 234.02666666666667,156.0554236327273 283.3916666666667,236.83197494781817 310.81666666666666,191.95611311454547' fill='LimeGreen' stroke-width='2'/><polygon points='283.3916666666667,236.83197494781817 310.81666666666666,191.95611311454547 402.23333333333335,341.54231922545455 347.3833333333333,341.54231922545455' fill='HotPink' stroke-width='2'/><polygon points='93.245,284.6995609109091 71.305,284.6995609109091 36.56666666666666,341.54231922545455 120.67000000000002,341.54231922545455 124.32666666666667,335.5588709727273 62.163333333333334,335.5588709727273' fill='blue' stroke-width='2'/><polygon points='155.40833333333333,284.6995609109091 93.245,284.6995609109091 62.163333333333334,335.5588709727273 124.32666666666667,335.5588709727273' fill='SkyBlue' stroke-width='2'/>";
        // these are the texts/labels outside the triangle
        $texts = "<text x='320' y='-190' transform='rotate(60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Ethylene (C₂H₄)</text><text x='215' y='365' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Acetylene (C₂H₂)</text><text x='-105' y='191' transform='rotate(-60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% Methane (CH₄)</text>";

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
            $dt1_total = $value['c2h4'] + $value['c2h2'] + $value['ch4']; //Getting the percentage of each value
            $c2h4_pc = ($value['c2h4'] / $dt1_total) * 100;
            $c2h2_pc = ($value['c2h2'] / $dt1_total) * 100;
            $ch4_pc = ($value['ch4'] / $dt1_total) * 100;

            // determining the x and y coordinates of the points
            $dt1_x = ($c2h4_pc - $c2h2_pc) * sin(30 * pi() / 180);
            $dt1_y = ($ch4_pc / 100) * $tr_h + $tr_ymin;
            // before the points can be plotted the coordinates should be scalled/translated 
            // to the proper quadrant and domain where the triangle is located in the plane
            $x = $this->scaling_x($dt1_x);
            $y = $this->scaling_y($dt1_y);
            // plot the scalled coordinates
            $circles .= "<circle r='{$this->circleSize($key)}' cx='{$x}' cy='{$y}' id='plot-t2-{$key}' style='fill: {$this->circleColor($key,$clean_length)}; cursor: pointer;'/>";

            // creating connectinglines
            if (count($coordinate_buffer) == 1) {
                $immediate_history = array_pop($coordinate_buffer); // pop the buffer

                // make the line using the coordinates from the buffer and the lastest coordinates
                // $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='#ffaa00' stroke-width='{$this->LINE_THICKNESS}'/>";
                $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='{$this->LINE_COLOR}' stroke-width='{$this->LINE_THICKNESS}'/>";
            }
            array_push($coordinate_buffer, ["x" => $x, "y" => $y]); // push to the buffer even if count($coordinate_buffer) is 1 or 0

            // genrate tooltip html
            $tooltipHTML .= "<div id='plot-t2-{$key}-pup' style='padding: 5px; font-size: 10px;background: #fff; position: absolute; display: none; visibility: hidden; font-family: calibri; border: 1px solid #cccccc'><table width='100%' class='basic-table' style='font-size:12px;'>";
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Timestamp</td><td>{$value['timestamp']}</td></tr>";
            $tooltipHTML .= sprintf(
                "<tr><td style='font-weight:bold;'>Acetylene (C₂H₂) </td><td>%s (%s%%)</td></tr>",
                number_format($value['c2h2'], 2),
                number_format($c2h2_pc, 2)
            );
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
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault </td><td>{$value['t2']}</td></tr>";
            // $code = $this->getDuvalsCode(array($x, $y));
            // $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault* </td><td>{$code}</td></tr>";
            $tooltipHTML .= "</table></div>";

            // add to the popup mapping
            array_push($popup_mapping, ["plot-t2-{$key}", "plot-t2-{$key}-pup"]);
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
