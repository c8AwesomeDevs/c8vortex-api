<?php
namespace App\Services\Interpretations;

use App\Contracts\Interpretations\NEIInterface;
use App\Services\RatioInterpretationService;

class NEIService extends RatioInterpretationService implements NEIInterface
{
    public function getResult() {
        $result = $this->valueComp($this->methane,$this->ethane, $this->ethylene, $this->acetylene );

        return $result;
    }

    private function valueComp($methane,$ethane,$ethylene,$acetylene){
        $computation = ((77.7 * $methane) + (93.5 * $ethane) + (104.1 * $ethylene) + (278.3 * $acetylene)) / 22400;
        return $computation;
    }
}