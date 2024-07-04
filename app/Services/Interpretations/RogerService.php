<?php
namespace App\Services\Interpretations;

use App\Contracts\Interpretations\RogerInterface;
use App\Services\RatioInterpretationService;

class RogerService extends RatioInterpretationService implements RogerInterface
{
    public function getResult() {
        $proc1 = $this->firstProc($this->methane, $this->hydrogen);
        $proc2 = $this->secondProc($this->ethane, $this->methane);
        $proc3 = $this->thirdProc($this->ethane, $this->ethylene);
        $proc4 = $this->fourthProc($this->acetylene, $this->ethylene);

        $code = $proc1 . $proc2 . $proc3 . $proc4;

        return $this->getInterpretationDetails($code);
    }

    function firstProc($methane, $hydrogen) {
      if ($hydrogen == 0 || $methane == 0) {
          return 3; // Return 3 if $hydrogen or $methane is 0 to avoid division by zero error
      } else {
          $result = $methane / $hydrogen;
          if ($result > 3) {
              return 2;
          } elseif ($result > 1 && $result <= 3) {
              return 1;
          } elseif ($result > 0 && $result <= 1) {
              return 0;
          } else {
              return 3;
          }
      }
    }
  
    private function secondProc($ethane, $methane) {
      if ($ethane == 0 || $methane == 0) {
          return 0; // Return 0 if $methane is 0 to avoid division by zero error
      } else {
          $result = $ethane / $methane;
          if ($result > 1) {
              return 1;
          } else {
              return 0;
          }
      }
  }
  

  private function thirdProc($ethelyne, $ethane) {
    if ($ethane == 0 || $ethelyne == 0) {
        return 0; // Return 0 if $ethane or $ethelyne is 0 to avoid division by zero error
    } else {
        $result = $ethelyne / $ethane;
        if ($result > 3) {
            return 2;
        } else {
            return 1;
        }
    }
}

private function fourthProc($acetylene, $ethelyne) {
    if ($acetylene == 0 || $ethelyne == 0) {
        return 3; // Return 0 if $ethelyne is 0 to avoid division by zero error
    } else {
        $result = $acetylene / $ethelyne;
        if ($result > 3) {
            return 2;
        } elseif ($result > 0.5) {
            return 1;
        } else {
            return 0;
        }
    }
}


    private function getInterpretationDetails($code) {
      if($code == 0000){
          return "0000051112 | NO INTERPRETATION";
        }
        else if($code == 0010){
          return "0000005111 | GENERAL CONDUCTOR OVERHEATING";
        }
        else if($code == 0100){
          return "0000005112 | OVERHEATING, TEMPERATURE FROM 200Â° TO 300Â°C";
        }
        else if($code == 1000 || $code == 2000){
          return "0000005113 | SLIGHT OVERHEATING, TEMPERATURE BELOW 150Â°C";
        } 
        else if($code == 1100 || $code == 2100){
          return "0000005114 | OVERHEATING, TEMPERATURE FROM 150Â° TO 200Â°C";
        }
        else if($code == 1010){
          return "0000005115 | WINDING CIRCULATING CURRENTS";
        }
        else if($code == 1020 || $code == 2020){
          return "0000005116 | CORE AND TANK CIRCULATING CURRENTS, OVERHEATED JOINTS";
        }
        else if($code == 3000){
          return "0000005117 | PARTIAL DISCHARGE";
        }
        else if($code == 3001 || $code == 3002){
          return "0000005118 | PARTIAL DISCHARGE WITH TRACING OF CARBON MONOXIDE";
        }
        else if($code == 0001){
          return "0000005119 | FLASHOVER WITHOUT POWER FOLLOW THROUGH";
        }
        else if($code == 0011 || $code == 0012 || $code == 0021){
          return "0000051110 | ARC WITH POWER FOLLOW THROUGH";
        }
        else if($code == 0022){
          return "0000051111 | CONTINUOUS SPARKING TO FAULTING POTENTIAL";
        }
        else{
          return "NO INTERPRETATION";
        }
  }

}