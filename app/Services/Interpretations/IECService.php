<?php
namespace App\Services\Interpretations;

use App\Contracts\Interpretations\IECInterface;
use App\Services\RatioInterpretationService;
class IECService extends RatioInterpretationService implements IECInterface
{
    public function getResult() {
        $proc1 = $this->firstProc($this->ethylene, $this->acetylene);
        $proc2 = $this->secondProc($this->methane, $this->hydrogen);
        $proc3 = $this->thirdProc($this->ethane, $this->ethylene);

        $code = $proc1 . $proc2 . $proc3;

        return $this->getInterpretationDetails($code);
    }

   
    private function firstProc($ethylene, $acetylene) {
        if ($ethylene == 0 || $acetylene == 0) {
            return 2; // Return 2 if $ethylene or $acetylene is 0 to avoid division by zero error
        } else{
            $result = $ethylene / $acetylene;
            if ($result < 0.1) {
                return 0;
            } elseif ($result <= 3) {
                return 1;
            } else {
                return 2;
            }
        }
    }
    
    private function secondProc($methane, $hydrogen) {
        if ($methane == 0 || $hydrogen == 0) {
            return 2; // Return 2 if $methane or $hydrogen is 0 to avoid division by zero error
        } else {
            $result = $methane / $hydrogen;
            if ($result < 0.1) {
                return 1;
            } elseif ($result <= 1) {
                return 0;
            } else {
                return 2;
            }
        }
    }
    
    private function thirdProc($ethane, $ethylene) {
        if ($ethane == 0 || $ethylene == 0) {
            return 0; // Return 2 if $ethane or $ethylene is 0 to avoid division by zero error
        } else {
            $result = $ethane / $ethylene;
            if ($result < 1) {
                return 0;
            } elseif ($result <= 3) {
                return 1;
            } else {
                return 2;
            }
        }
    }
    

    private function getInterpretationDetails($code) {
        if($code == 000) {
            return "0000005050 | NO FAULT";
        }
        elseif($code == 001) {
            return "0000005051 | THERMAL FAULT (TEMPERATURE < 150C)";
        }
        elseif($code == 020) {
            return "0000005052 | THERMAL FAULT (TEMPERATURE BETWEEN 150 TO 300C)";
        }
        else if($code == 021) {
            return "0000005053 | THERMAL FAULT (TEMPERATURE BETWEEN 300 TO 700C";
        }
        else if($code == 022) {
            return "0000005054 | THERMAL FAULT (TEMPERATURE > 700C)";
        }
        else if($code == 010) {
            return "0000005055 | PARTIAL DISCHARGE OF LOW ENERGY";
        }
        else if($code == 110) {
            return "0000005056 | PARTIAL DISCHARGE OF HIGH ENERGY";
        }
        else if($code == 101 || $code == 201 || $code == 202) {
            return "0000005057 | DISCHARGES OF LOW ENERGY";
        }
        else if($code == 102) {
            return "0000005058 | DISCHARGES OF HIGH ENERGY";
        }
        else{
            return "0000005059 | NO INTERPRETATION";
        }
    }

}