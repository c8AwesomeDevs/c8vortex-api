<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SVGgeneration\SVGHelpers;
use App\Services\SVGgeneration\T1SVGService;
use App\Services\SVGgeneration\T2SVGService;
use App\Services\SVGgeneration\T3SVGService;
use App\Services\SVGgeneration\T4SVGService;
use App\Services\SVGgeneration\T5SVGService;
use App\Services\SVGgeneration\T6SVGService;
use App\Services\SVGgeneration\T7SVGService;
use App\Services\SVGgeneration\P1SVGService;
use App\Services\SVGgeneration\P2SVGService;

class SVGController extends Controller
{
    private $helpers, $t1, $t2, $t3, $t4, $t5, $t6, $t7, $p1, $p2;

    public function __construct()
    {
        $this->helpers = new SVGHelpers;
        $this->t1 = new T1SVGService;
        $this->t2 = new T2SVGService;
        $this->t3 = new T3SVGService;
        $this->t4 = new T4SVGService;
        $this->t5 = new T5SVGService;
        $this->t6 = new T6SVGService;
        $this->t7 = new T7SVGService;
        $this->p1 = new P1SVGService;
        $this->p2 = new P2SVGService;
    }

    private function concatSVGparts($parts)
    {
        return $this->helpers->OPENSVG . $this->helpers->OPENGROUPING . $parts['polygons'] . $parts['connectingLines'] . $parts['circles'] . $this->helpers->CLOSEGROUPING . $parts['texts'] . $this->helpers->CLOSESVG;
    }

    private function concatSVGparts_penta($parts)
    {
        return $this->helpers->OPENSVG_PENTA . $this->helpers->OPENGROUPING . $parts['polygons'] . $parts['connectingLines'] . $parts['circles'] . $this->helpers->CLOSEGROUPING . $parts['texts'] . $this->helpers->CLOSESVG;
    }

    public function assembleSVGs($request)
    {
        // get parts from the generators
        $t1_parts = $this->t1->gen_t1($request);
        $t2_parts = $this->t2->gen_t2($request);
        $t3_parts = $this->t3->gen_t3($request);
        $t4_parts = $this->t4->gen_t4($request);
        $t5_parts = $this->t5->gen_t5($request);
        $t6_parts = $this->t6->gen_t6($request);
        $t7_parts = $this->t7->gen_t7($request);
        $p1_parts = $this->p1->gen_p1($request);
        $p2_parts = $this->p2->gen_p2($request);

        // create a long string of all popup for all symbols
        $popups = "";
        $popups .= $t1_parts['tooltipHTML'];
        $popups .= $t2_parts['tooltipHTML'];
        $popups .= $t3_parts['tooltipHTML'];
        $popups .= $t4_parts['tooltipHTML'];
        $popups .= $t5_parts['tooltipHTML'];
        $popups .= $t6_parts['tooltipHTML'];
        $popups .= $t7_parts['tooltipHTML'];
        $popups .= $p1_parts['tooltipHTML'];
        $popups .= $p2_parts['tooltipHTML'];

        // create a collection of mappings; the map of each popup to each circle 
        $popup_mappings = array_merge(
            $t1_parts['popup_mapping'],
            $t2_parts['popup_mapping'],
            $t3_parts['popup_mapping'],
            $t4_parts['popup_mapping'],
            $t5_parts['popup_mapping'],
            $t6_parts['popup_mapping'],
            $t7_parts['popup_mapping'],
            $p1_parts['popup_mapping'],
            $p2_parts['popup_mapping']
        );

        // assemble response
        return response()->json([
            "t1" => $this->concatSVGparts($t1_parts), // Acetylene (C₂H₂), Ethylene (C₂H₄), Methane (CH₄)
            "t2" => $this->concatSVGparts($t2_parts), // Acetylene (C₂H₂), Ethylene (C₂H₄), Methane (CH₄)
            "t3" => $this->concatSVGparts($t3_parts), // Acetylene (C₂H₂), Ethylene (C₂H₄), Methane (CH₄)
            "t4" => $this->concatSVGparts($t4_parts), // Methane (CH₄), Ethane (C₂H₆), Dihydrogen (H₂)
            "t5" => $this->concatSVGparts($t5_parts), // Ethylene (C₂H₄), Methane (CH₄), Ethane (C₂H₆)
            "t6" => $this->concatSVGparts($t6_parts), // Methane (CH₄), Ethane (C₂H₆), Dihydrogen (H₂) 
            "t7" => $this->concatSVGparts($t7_parts), // Ethylene (C₂H₄), Methane (CH₄), Ethane (C₂H₆)
            "p1" => $this->concatSVGparts_penta($p1_parts),
            "p2" => $this->concatSVGparts_penta($p2_parts),
            "popups" => $popups,
            "popup_mappings" => $popup_mappings
        ], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES);
        // return $this->concatSVGparts($t1_parts);
    }
}
