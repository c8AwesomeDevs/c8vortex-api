<?php

namespace App\Services;

class RatioInterpretationService {
    protected $acetylene, $ethylene, $methane, $ethane, $hydrogen;

    public function __construct($acetylene, $ethylene, $methane, $ethane, $hydrogen) {
        $this->acetylene = $acetylene;
        $this->ethylene = $ethylene;
        $this->methane = $methane;
        $this->ethane = $ethane;
        $this->hydrogen = $hydrogen;
    }
}