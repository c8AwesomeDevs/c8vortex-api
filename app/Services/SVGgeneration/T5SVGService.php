<?php

namespace App\Services\SVGgeneration;

use App\Services\SVGgeneration\SVGHelpers;

class T5SVGService extends SVGHelpers
{
    // private $regions = [
    //     "PD" => array(array(214.82916666666665, 49.84909090909095), array(191.975, 87.24454545454545), array(191.975, 93.22781818181817), array(216.65750000000003, 52.840727272727264)),
    //     // there are 2 O polygons
    //     "O-1" => array(array(219.4, 42.36990700363636), array(214.82916666666665, 49.84909090909095), array(216.65750000000003, 52.840727272727264), array(191.975, 93.22781818181817), array(210.25833333333335, 117.16436363636362), array(237.68333333333334, 72.28636363636362)),
    //     "O-2" => array(array(120.67000000000002, 203.92181818181817), array(36.56666666666666, 341.54231922545455), array(73.13333333333333, 341.54231922545455), array(138.95333333333332, 233.84025454545457)),
    //     // there are 2 T3 polygons
    //     "T3-1" => array(array(283.3916666666667, 147.08072727272727), array(261.45166666666665, 182.98036363636368), array(288.87666666666667, 227.85663636363637), array(285.22, 233.84025454545457), array(321.78666666666663, 293.6709090909091), array(292.5333333333333, 341.54231922545455), array(402.23333333333335, 341.54231922545455)),
    //     "T3-2" => array(array(228.54166666666669, 236.83189090909093), array(164.55, 341.54231922545455), array(292.5333333333333, 341.54231922545455)),
    //     "T2" => array(array(237.68333333333334, 72.28636363636362), array(215.74333333333334, 108.18945454545455), array(261.45166666666665, 182.98036363636368), array(283.3916666666667, 147.08072727272727)),
    //     "S" => array(array(191.975, 87.24454545454545), array(120.67000000000002, 203.92181818181817), array(138.95333333333332, 233.84025454545457), array(210.25833333333335, 117.16436363636362)),
    //     "C" => array(array(215.74333333333334, 108.18945454545455), array(182.83333333333334, 162.0389090909091), array(292.5333333333333, 341.54231922545455), array(321.78666666666663, 293.6709090909091), array(285.22, 233.84025454545457), array(288.87666666666667, 227.85663636363637)),
    //     "ND" => array(array(182.83333333333334, 162.0389090909091), array(73.13333333333333, 341.54231922545455), array(164.55, 341.54231922545455), array(228.54166666666669, 236.83189090909093)),
    // ];

    // private function getDuvalsCode($point)
    // {
    //     // Check if the point is inside each polygon
    //     if ($this->pointInPolygon($point, $this->regions['PD'])) {
    //         return "PD";
    //     } elseif ($this->pointInPolygon($point, $this->regions['O-1'])) {
    //         return "O";
    //     } elseif ($this->pointInPolygon($point, $this->regions['O-2'])) {
    //         return "O";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T3-1'])) {
    //         return "T3";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T3-2'])) {
    //         return "T3";
    //     } elseif ($this->pointInPolygon($point, $this->regions['T2'])) {
    //         return "T2";
    //     } elseif ($this->pointInPolygon($point, $this->regions['S'])) {
    //         return "S";
    //     } elseif ($this->pointInPolygon($point, $this->regions['C'])) {
    //         return "C";
    //     } elseif ($this->pointInPolygon($point, $this->regions['ND'])) {
    //         return "ND";
    //     } else {
    //         return "N/A";
    //     }
    // }

    public function gen_t5($data)
    {
        // do not plot data with 'Normal' interpretation
        // we use array_filter to only get what we want
        // we use array_values to reset the indexes back to 0
        $clean = array_values(array_filter($data, function ($v, $k) {
            return $v['t5'] != 'Normal';
        }, ARRAY_FILTER_USE_BOTH));

        // these are the zones/regions of the triangle
        $polygons = "<polygon points='214.82916666666665,49.84909090909095 191.975,87.24454545454545 191.975,93.22781818181817 216.65750000000003,52.840727272727264' fill='pink' stroke-width='2'/><polygon points='219.4,42.36990700363636 214.82916666666665,49.84909090909095 216.65750000000003,52.840727272727264 191.975,93.22781818181817 210.25833333333335,117.16436363636362 237.68333333333334,72.28636363636362' fill='purple' stroke-width='2'/><polygon points='237.68333333333334,72.28636363636362 215.74333333333334,108.18945454545455 261.45166666666665,182.98036363636368 283.3916666666667,147.08072727272727' fill='LimeGreen' stroke-width='2'/><polygon points='283.3916666666667,147.08072727272727 261.45166666666665,182.98036363636368 288.87666666666667,227.85663636363637 285.22,233.84025454545457 321.78666666666663,293.6709090909091 292.5333333333333,341.54231922545455 402.23333333333335,341.54231922545455' fill='HotPink' stroke-width='2'/><polygon points='228.54166666666669,236.83189090909093 164.55,341.54231922545455 292.5333333333333,341.54231922545455' fill='HotPink' stroke-width='2'/><polygon points='120.67000000000002,203.92181818181817 36.56666666666666,341.54231922545455 73.13333333333333,341.54231922545455 138.95333333333332,233.84025454545457' fill='purple' stroke-width='2'/><polygon points='191.975,87.24454545454545 120.67000000000002,203.92181818181817 138.95333333333332,233.84025454545457 210.25833333333335,117.16436363636362' fill='blue' stroke-width='2'/><polygon points='215.74333333333334,108.18945454545455 182.83333333333334,162.0389090909091 292.5333333333333,341.54231922545455 321.78666666666663,293.6709090909091 285.22,233.84025454545457 288.87666666666667,227.85663636363637' fill='SkyBlue' stroke-width='2'/><polygon points='182.83333333333334,162.0389090909091 73.13333333333333,341.54231922545455 164.55,341.54231922545455 228.54166666666669,236.83189090909093' fill='#679A00' stroke-width='2'/>";
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
            $circles .= "<circle r='{$this->circleSize($key)}' cx='{$x}' cy='{$y}' id='plot-t5-{$key}' style='fill: {$this->circleColor($key,$clean_length)}; cursor: pointer;'/>";

            // creating connectinglines
            if (count($coordinate_buffer) == 1) {
                $immediate_history = array_pop($coordinate_buffer); // pop the buffer

                // make the line using the coordinates from the buffer and the lastest coordinates
                // $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='#ffaa00' stroke-width='{$this->LINE_THICKNESS}'/>";
                $connectingLines .= "<line x1='{$immediate_history["x"]}' y1='{$immediate_history["y"]}' x2='{$x}' y2='{$y}' stroke='{$this->LINE_COLOR}' stroke-width='{$this->LINE_THICKNESS}'/>";
            }
            array_push($coordinate_buffer, ["x" => $x, "y" => $y]); // push to the buffer even if count($coordinate_buffer) is 1 or 0

            // genrate tooltip html
            $tooltipHTML .= "<div id='plot-t5-{$key}-pup' style='padding: 5px; font-size: 10px;background: #fff; position: absolute; display: none; visibility: hidden; font-family: calibri; border: 1px solid #cccccc'><table width='100%' class='basic-table' style='font-size:12px;'>";
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
            $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault </td><td>{$value['t5']}</td></tr>";
            // $code = $this->getDuvalsCode(array($x, $y));
            // $tooltipHTML .= "<tr><td style='font-weight:bold;'>Fault* </td><td>{$code}</td></tr>";
            $tooltipHTML .= "</table></div>";

            // add to the popup mapping
            array_push($popup_mapping, ["plot-t5-{$key}", "plot-t5-{$key}-pup"]);
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
