<?php

namespace App\Services;

class DuvalInterpretationService
{
    protected $ethylene, $methane,  $ethane,  $hydrogen, $acetylene, $acetylene_roc, $ethylene_roc, $methane_roc, $ethane_roc, $hydrogen_roc;

    public function __construct( $acetylene,  $ethylene, $methane, $ethane,  $hydrogen, $acetylene_roc,  $ethylene_roc, $methane_roc, $ethane_roc, $hydrogen_roc) {
        $this->acetylene = $acetylene;
        $this->ethylene = $ethylene;
        $this->methane = $methane;
        $this->ethane = $ethane;
        $this->hydrogen = $hydrogen;
        $this->acetylene_roc = $acetylene_roc;
        $this->ethylene_roc = $ethylene_roc;
        $this->methane_roc = $methane_roc;
        $this->ethane_roc = $ethane_roc;
        $this->hydrogen_roc = $hydrogen_roc;
       
       
    }
}