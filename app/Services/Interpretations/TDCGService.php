<?php
namespace App\Services\Interpretations;

use App\Contracts\Interpretations\TDCGInterface;

class TDCGService implements TDCGInterface
{   
    protected $acetylene, $ethylene, $methane, $ethane, $hydrogen, $carbon_monoxide;

    public function __construct($acetylene, $ethylene, $methane, $ethane, $hydrogen, $carbon_monoxide) {
        $this->acetylene = $acetylene;
        $this->ethylene = $ethylene;
        $this->methane = $methane;
        $this->ethane = $ethane;
        $this->hydrogen = $hydrogen;
        $this->carbon_monoxide = $carbon_monoxide;
    }

    public function getResult() {
        //Sum of these gasses (H2), methane (CH4), ethane (C2H6), ethylene (C2H4), acetylene (C2H2), and carbon monoxide (CO)

        // return $this->hydrogen + $this->methane + $this->ethane + $this->ethylene + $this->acetylene + $this->carbon_monoxide;

        return $this->acetylene + $this->ethylene + $this->methane + $this->ethane + $this->hydrogen + $this->carbon_monoxide;
    }
}