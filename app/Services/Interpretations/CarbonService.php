<?php
namespace App\Services\Interpretations;

use App\Contracts\Interpretations\CarbonInterface;
class CarbonService implements CarbonInterface
{
    protected $carbon_dioxide, $carbon_monoxide;

    public function __construct($carbon_dioxide, $carbon_monoxide){
        $this->carbon_dioxide = $carbon_dioxide;
        $this->carbon_monoxide = $carbon_monoxide;

    }

    public function getResult() {
        $code = $this->firstProc($this->carbon_dioxide, $this->carbon_monoxide);
        
        return $code;
    }
    public function firstProc() {
    if ($this->carbon_dioxide == 0 || $this->carbon_monoxide == 0)
    {
        return "No Interpretation";
    }else{
        $ratio = $this->carbon_dioxide / $this->carbon_monoxide;

        if ($ratio == 7) {
              return "Normal";
          } else if ($ratio == 6 || $ratio == 8) {
              return "CAUTION: Indicates Thermal decomposition of cellulose";
          } else if ($ratio == 4.5 || ($ratio >= 9 && $ratio <= 10)) {
              return "WARNING: An excellent indication of abnormally high temperature and rapidly deteriorating cellulose under 5";
          } else if ($ratio <= 3 || $ratio > 10) {
              return "CRITICAL: Severe and rapid deteriotion of cellulose is occuring. Extreme overheating from loss of cooling or plugged oil passage will produce a CO2/CO Ratio around 2 and 3 along with increasing Furans";
          } else {
              return "No Interpretation";
          }
        }
    }
     


}