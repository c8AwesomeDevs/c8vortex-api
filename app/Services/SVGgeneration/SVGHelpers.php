<?php

namespace App\Services\SVGgeneration;

// define('OPENSVG', "<svg preserveAspectRatio='xMinYMin meet' viewBox='0 0 438.8 380'>");
// define('CLOSESVG', "</svg>");
// define('OPENGROUPING', "<g transform='translate(0,0)'>");
// define('CLOSEGROUPING', "</g>");

// define('DOMAIN_XMIN', -60);
// define('DOMAIN_XMAX', 60);
// define('RANGE_XMIN', 0);
// define('RANGE_XMAX', 438.8);

// define('DOMAIN_YMIN', -35);
// define('DOMAIN_YMAX', 70);
// define('RANGE_YMIN', 380);
// define('RANGE_YMAX', 0);

// define('POINTSIZE_LASTEST', 4);
// define('POINTSIZE_HISTORY', 2.4);
// define('LINE_THICKNESS', 1);


// // define('T1POLYGONS', "<polygon points='219.4,42.36990700363636 215.74333333333334,48.35327272727273 223.05666666666667,48.35327272727273' fill='pink' stroke-width='2'/><polygon points='223.05666666666667,48.35327272727273 215.74333333333334,48.35327272727273 212.08666666666667,54.336545454545444 248.65333333333334,114.17272727272726 255.9666666666667,102.20272727272732' fill='purple' stroke-width='2'/><polygon points='255.9666666666667,102.20272727272732 248.65333333333334,114.17272727272726 303.50333333333333,203.92300962000002 310.81666666666666,191.95611311454547' fill='green' stroke-width='2'/><polygon points='310.81666666666666,191.95611311454547 283.3916666666667,236.83197494781817 347.3833333333333,341.54231922545455 402.23333333333335,341.54231922545455' fill='HotPink' stroke-width='2'/><polygon points='212.08666666666667,54.336545454545444 195.6316666666667,81.26232059454546 268.76500000000004,200.93128547636366 239.51166666666666,248.798871436 296.19000000000005,341.54231922545455 347.3833333333333,341.54231922545455 283.3916666666667,236.83197494781817 303.50333333333333,203.92300962000002' fill='blue' stroke-width='2'/><polygon points='195.6316666666667,81.26232059454546 36.56666666666666,341.54231922545455 120.67000000000002,341.54231922545455 237.68333333333334,150.07197541454545' fill='SkyBlue' stroke-width='2'/><polygon points='237.68333333333334,150.07197541454545 120.67000000000002,341.54231922545455 296.19000000000005,341.54231922545455 239.51166666666666,248.798871436 268.76500000000004,200.93128547636366' fill='#679A00' stroke-width='2'/>");
// // define('T1CIRCLES', "<circle r='4' cx='219.4' cy='241.81818181818187' id='plot-triangle1-0' style='fill: rgb(255, 0, 0); cursor: pointer;'/>");
// // define('T1TEXTS', "<text x='320' y='-190' transform='rotate(60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; stroke: black; font-family: calibri;'>% C2H4</text><text x='215' y='365' text-anchor='middle' style='font-size: 11px; fill: green; stroke-width: 0px; font-family: calibri;'>% C2H2</text><text x='-105' y='191' transform='rotate(-60)' text-anchor='middle' style='font-size: 10px; fill: green; stroke-width: 0px; font-family: calibri;'>% CH4</text>");

class SVGHelpers
{
    public $OPENSVG = "<svg preserveAspectRatio='xMinYMin meet' viewBox='0 0 438.8 380'>";
    public $OPENSVG_PENTA = "<svg preserveAspectRatio='xMinYMin meet' viewBox='0 0 535 500'>";
    public $CLOSESVG = "</svg>";
    public $OPENGROUPING = "<g transform='translate(0,0)'>";
    public $CLOSEGROUPING = "</g>";

    public $DOMAIN_XMIN = -60;
    public $DOMAIN_XMAX = 60;
    public $RANGE_XMIN = 0;
    public $RANGE_XMAX = 438.8;

    public $DOMAIN_YMIN = -35;
    public $DOMAIN_YMAX = 70;
    public $RANGE_YMIN = 362.5; // this was 380; but it was off by about 10-15 units 
    public $RANGE_YMAX = 0.1;

    public $PENTA_DOMAIN_XMIN = -40;
    public $PENTA_DOMAIN_XMAX = 50;
    public $PENTA_RANGE_XMIN = 0;
    public $PENTA_RANGE_XMAX = 600;

    public $PENTA_DOMAIN_YMIN = -60;
    public $PENTA_DOMAIN_YMAX = 40;
    public $PENTA_RANGE_YMIN = 650;
    public $PENTA_RANGE_YMAX = 0;

    public $POINTSIZE_LASTEST = 4;
    // public $POINTCOLOR_LASTEST = '#FF0000';
    public $POINTSIZE_HISTORY = 2.4;
    // public $POINTCOLOR_HISTORY = '#5b5b5b';
    public $LINE_THICKNESS = 1;
    public $LINE_COLOR = '#FFA500';


    public function scaling_x($value, $mode = 'triangle')
    {
        switch ($mode) {
            case 'triangle':
                $domain_x_min = $this->DOMAIN_XMIN;
                $domain_x_max = $this->DOMAIN_XMAX;
                $range_x_min = $this->RANGE_XMIN;
                $range_x_max = $this->RANGE_XMAX;
                break;

            case 'pentagon':
                $domain_x_min = $this->PENTA_DOMAIN_XMIN;
                $domain_x_max = $this->PENTA_DOMAIN_XMAX;
                $range_x_min = $this->PENTA_RANGE_XMIN;
                $range_x_max = $this->PENTA_RANGE_XMAX;
                break;
            default:
                return 0;
        }

        # Ensure the value is within the input range
        $value = max(min($value, $domain_x_max), $domain_x_min);

        # Scale the value from the input range to the output range
        $scaled_value = ($value - $domain_x_min) * ($range_x_max - $range_x_min) / ($domain_x_max - $domain_x_min) + $range_x_min;

        return $scaled_value;
    }
    public function scaling_y($value, $mode = 'triangle')
    {
        switch ($mode) {
            case 'triangle':
                $domain_y_min = $this->DOMAIN_YMIN;
                $domain_y_max = $this->DOMAIN_YMAX;
                $range_y_min = $this->RANGE_YMIN;
                $range_y_max = $this->RANGE_YMAX;
                break;

            case 'pentagon':
                $domain_y_min = $this->PENTA_DOMAIN_YMIN;
                $domain_y_max = $this->PENTA_DOMAIN_YMAX;
                $range_y_min = $this->PENTA_RANGE_YMIN;
                $range_y_max = $this->PENTA_RANGE_YMAX;
                break;
            default:
                return 0;
        }

        # Ensure the value is within the input range
        $value = max(min($value, $domain_y_max), $domain_y_min);

        # Scale the value from the input range to the output range
        $scaled_value = ($value - $domain_y_min) * ($range_y_max - $range_y_min) / ($domain_y_max - $domain_y_min) + $range_y_min;

        return $scaled_value;
    }

    
    public function circleSize($key)
    {
        return $key == 0 ? $this->POINTSIZE_LASTEST : $this->POINTSIZE_HISTORY;
    }
    public function circleColor($key, $max)
    {
        // Initialize RGB values
        $r = 255;
        $g = 0;
        $b = 0;

        // Check if $max is not zero before calculating the interval
        $interval = ($max != 0) ? 255 / $max : 0;

        // Update green component based on index (green gradient for older entries)
        $g = floor($key * $interval);

        // Set opacity based on the color being red or not
        $opacity = ($g == 0) ? 2.5 : 0.8;

        // Convert RGB and opacity to RGBA format
        $rgbaColor = "rgba($r, $g, $b, $opacity)";

        return $rgbaColor;
    }



    // Function to check if a point is inside a polygon
    public function pointInPolygon($point, $polygon)
    {
        // this function was gracefully stolen from chatgpt
        // it plots the polygon's vertices (one by one) and the point
        // it then draws a line from the point to the right
        // if the number of intersections between the line and the vertices is even;
        //      the point is OUTSIDE the polygon
        // if the number of intersections is odd the point is INSIDE the polygon
        $numVertices = count($polygon);
        $j = $numVertices - 1;
        $inside = false;

        for ($i = 0; $i < $numVertices; $i++) {
            if (
                ($polygon[$i][1] < $point[1] && $polygon[$j][1] >= $point[1] ||
                    $polygon[$j][1] < $point[1] && $polygon[$i][1] >= $point[1]) &&
                ($polygon[$i][0] <= $point[0] || $polygon[$j][0] <= $point[0])
            ) {
                if ($polygon[$i][0] + ($point[1] - $polygon[$i][1]) / ($polygon[$j][1] - $polygon[$i][1]) * ($polygon[$j][0] - $polygon[$i][0]) < $point[0]) {
                    $inside = !$inside;
                }
            }
            $j = $i;
        }

        return $inside;
    }
}
