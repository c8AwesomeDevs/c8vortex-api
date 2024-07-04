<?php
namespace App\Services\Interpretations;

use App\Contracts\Interpretations\DornenbergInterface;
use App\Services\RatioInterpretationService;
class DornenbergService extends RatioInterpretationService implements DornenbergInterface
{
    public function getResult() {
        $proc1 = $this->firstProc($this->hydrogen, $this->methane, $this->acetylene, $this->ethylene, $this->ethane);
        
        $code = $proc1;

        return $this->getInterpretationDetails($code);
    }

    private function firstProc($hydrogen,$methane,$acetylene,$ethylene,$ethane) {
      if($hydrogen <= 0 || $ethylene <= 0 || $acetylene <= 0 || $methane <= 0 || $ethane <= 0)
      {
        return 4;
      }else{
        $result1 = $methane / $hydrogen;
        $result2 = $acetylene / $ethylene;
        $result3 = $ethane / $acetylene;
        $result4 = $acetylene / $methane;
    
        if($result1 > 1 || $result2 < 0.75 || $result3 > 0.40 || $result4 < 0.3){
          return 1;
        }
        else if($result1 > 0.1 || $result2 < 0.75 || $result3 > 0.40 || $result4 < 0.3){
          return 2;
        }else if($result1 > 0.1 || $result3 > 0.40 || $result4 < 0.3){
          return 3;
        }
        else{
          return 4;
        }
      }
      
    }
    
    private function getInterpretationDetails($code) {

      if($code == 1){
          return "0000005123 | NO INTERPRETATION";
        }
        else if($code == 2){
          return "0000005120 | FAULT TYPE IS THERMAL DECOMPOSITION (HOT SPOTS)";
        }
        else if($code == 3){
          return "0000005121 | FAULT TYPE IS ELECTRICAL DISCHARGE (EXCEPT CORONA)";
        }
        else if($code == 4){
          return "0000005122 | FAULT TYPE IS CORONA";
        }
  }

}