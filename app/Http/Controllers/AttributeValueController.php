<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttributeValueService;
use App\Services\Interpretations\CarbonService;
use App\Services\Interpretations\IECService;
use App\Services\Interpretations\NEIService;
use App\Services\Interpretations\DornenbergService;
use App\Services\Interpretations\RogerService;
use App\Services\Interpretations\TDCGService;
use App\Services\DuvalInterpretations\TriangleOneService;
use App\Services\DuvalInterpretations\TriangleTwoService;
use App\Services\DuvalInterpretations\TriangleThreeService;
use App\Services\DuvalInterpretations\TriangleFourService;
use App\Services\DuvalInterpretations\TriangleFiveService;
use App\Services\DuvalInterpretations\TriangleSixService;
use App\Services\DuvalInterpretations\TriangleSevenService;
use App\Services\DuvalInterpretations\PentagonOneService;
use App\Services\DuvalInterpretations\PentagonTwoService;
use App\Services\CommentsService;
use App\Services\ElementService;
use App\Services\CompanyService;
use App\Services\StripeService;
use App\Services\SubscriptionService;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\ADHService;
use Carbon\Carbon;

use App\Http\Controllers\SVGController;

class AttributeValueController extends Controller
{
    private function getSummary($request)
    {

        $carbonService = new CarbonService($request->carbon_dioxide, $request->carbon_monoxide);
        $carbon_result = $carbonService->getResult();

        $iecService = new IECService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen);
        $iec_ratio = $iecService->getResult();

        $dornenbergService = new DornenbergService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen);
        $dornenberg_result = $dornenbergService->getResult();

        $neiService = new NEIService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen);
        $nei_result = $neiService->getResult();

        $rogerService = new RogerService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen);
        $roger_result = $rogerService->getResult();

        $tdcgService = new TDCGService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->carbon_monoxide);
        $tdcg_result = $tdcgService->getResult();

        $triangleOneService = new TriangleOneService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_one_result = $triangleOneService->getResult();

        $triangleTwoService = new TriangleTwoService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_two_result = $triangleTwoService->getResult();

        $triangleThreeService = new TriangleThreeService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_three_result = $triangleThreeService->getResult();

        $triangleFourService = new TriangleFourService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_four_result = $triangleFourService->getResult();

        $triangleFiveService = new TriangleFiveService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_five_result = $triangleFiveService->getResult();

        $triangleSixService = new TriangleSixService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_six_result = $triangleSixService->getResult();

        $triangleSevenService = new TriangleSevenService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $triangle_seven_result = $triangleSevenService->getResult();

        $pentagonOneService = new PentagonOneService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $pentagon_one_result = $pentagonOneService->getResult();

        $pentagonTwoService = new PentagonTwoService($request->acetylene, $request->ethylene, $request->methane, $request->ethane, $request->hydrogen, $request->acetylene_roc, $request->ethylene_roc, $request->methane_roc, $request->ethane_roc, $request->hydrogen_roc);
        $pentagon_two_result = $pentagonTwoService->getResult();

        $summary = [
            'carbon_ratio' => $carbon_result,
            'iec_ratio' => $iec_ratio,
            'dornenberg' => $dornenberg_result,
            'nei' => $nei_result,
            'rogers_ratio' => $roger_result,
            'tdcg' => $tdcg_result,
            't1' => $triangle_one_result,
            't2' => $triangle_two_result,
            't3_biotemp' => $triangle_three_result,
            't3_fr' => $triangle_three_result,
            't3_midel' => $triangle_three_result,
            't3_silicon' => $triangle_three_result,
            't4' => $triangle_four_result,
            't5' => $triangle_five_result,
            't6' => $triangle_six_result,
            't7' => $triangle_seven_result,
            'p1' => $pentagon_one_result,
            'p2' => $pentagon_two_result
        ];

        return $summary;
    }

    public function get(Request $request,ElementService $elementservice, ADHService $adhService, CommentsService $commentsService, AttributeValueService $attributeValueService, $id)
    {   
      

        $start = $request->start;
        $end = $request->end;
        $id = $id;
        $startIndex = Carbon::parse($start)->format('M j, Y, g:i:s A');
        $endIndex = Carbon::parse($end)->format('M j, Y, g:i:s A');
      
        $adh_config = $adhService->getAdh($request->company_id);
        if (empty($adh_config)) {
            return response()->json(['error' => 'ADH configuration not found'], 400);
        }
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return response()->json(['error' => 'Failed to retrieve access token'], 400);
        }

        $element_details = $elementservice->getElementDetails($id);
        $stream_id = $adh_config[0]['stream_id'] ?? null;
        $adh_value = $this->getDataToADH($accessToken, $stream_id, $startIndex, $endIndex, $id);
        // Decode the JSON string into an array
        $adh_value = json_decode($adh_value, true);
        
        $latest_value = null;
        
        if (!empty($adh_value)) {
            // Sort the array by timestamp in descending order
            usort($adh_value, function ($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
        
            // Get the latest value (first element in the sorted array)
            $latest_value = $adh_value[0];
        } else {
            // Handle the case where $adh_value is empty
            $adh_value = [];
        }

     // Prepare the merged data
        $merged_data = [];

        // Iterate over each ADH value and merge with element details
        foreach ($adh_value as $value) {
            $merged_data[] = array_merge($value, [
                'element_id' => $element_details['id'],
                'name' => $element_details['name'],
                'description' => $element_details['description'],
                'path' => $element_details['path'],
                'parent_id' => $element_details['parent_id'],
                'has_child' => $element_details['has_child'],
                'created_at' => $element_details['created_at'],
                // Add more fields from element details as needed
            ]);
        }

        


        $values = $attributeValueService->getAttributeValues($id, $request->start, $request->end);
        $latest = $attributeValueService->getLatestAttributeValue($id, $request->start, $request->end);

        // $previous = $attributeValueService->getPreviousAttributeValue($id, $request->start, $request->end);

        $svgs = new SVGController();
        $rawdata_for_svgs = array();
        foreach ($merged_data as $key => $value) {
            array_push($rawdata_for_svgs, array(
                "timestamp" => $value['timestamp'] ?? null,  
                "c2h2" => $value['acetylene'] ?? null,       
                "c2h4" => $value['ethylene'] ?? null,        
                "ch4" => $value['methane'] ?? null,          
                "c2h6" => $value['ethane'] ?? null,          
                "c2h2_roc" => $value['acetylene_roc'] ?? null, 
                "c2h4_roc" => $value['ethylene_roc'] ?? null,  
                "ch4_roc" => $value['methane_roc'] ?? null,    
                "c2h6_roc" => $value['ethane_roc'] ?? null,    
                "h2_roc" => $value['hydrogen_roc'] ?? null,    
                "co" => $value['carbon_monoxide'] ?? null,     
                "co2" => $value['carbon_dioxide'] ?? null,     
                "h2" => $value['hydrogen'] ?? null,            
                "n2" => $value['nitrogen'] ?? null,            
                "t1" => $value['t1'] ?? null,                  
                "t2" => $value['t2'] ?? null,                  
                "t3" => $value['t3_biotemp'] ?? null,          
                "t4" => $value['t4'] ?? null,                  
                "t5" => $value['t5'] ?? null,                  
                "t6" => $value['t6'] ?? null,                  
                "t7" => $value['t7'] ?? null,                  
                "p1" => $value['p1'] ?? null,                  
                "p2" => $value['p2'] ?? null,                  // Default to null if not set
            ));
        }

        return response()->json([
            'adh_value' => $adh_value,
            'values' => $merged_data,
            'current' => $latest_value,
            'merge_Data' => $merged_data,
            // 'previous' => $previous,
            'svgs' => $svgs->assembleSVGs($rawdata_for_svgs)
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function saveAttributeValue(Request $request, CompanyService $companyService, AttributeValueService $attributeValueService, ADHService $adhService, SubscriptionService $subscriptionService, StripeService $stripeService, $id)
    {
        // Get ADH Configuration
        $adh_config = $adhService->getAdh($request->company_id);
        if (empty($adh_config)) {
            return response()->json(['error' => 'ADH configuration not found'], 400);
        }

        $stream_id = $adh_config[0]['stream_id'] ?? null;
        if (!$stream_id) {
            return response()->json(['error' => 'Tenants or Namespace not found in ADH configuration'], 400);
        }

        // Validate Request
        $this->validateRequest($request);

        $valuesOnRecord = $attributeValueService->countValuesByElementID($id);
        $limits = $companyService->getCompanyDetails($request->company_id);
        if ($valuesOnRecord['values'] == $limits["max_datapoints"]) {
            return response()->json(["error" => "You've reached the limit for 'Datapoint' elements"], 400);
        }

        // Handle Timestamps
        $timestamp = Carbon::parse($request->date . ' ' . $request->time)->format('Y-m-d H:i:s');
        $formattedADHTimestamp = Carbon::parse($request->date . ' ' . $request->time, 'Asia/Manila')->setTimezone('UTC')->format('Y-m-d H:i:s');

        if ($attributeValueService->dataExists($id, $timestamp)) {
            return response()->json(['error' => 'Data with this timestamp already exists.'], 400);
        }

        // Calculate Rate of Change
        $latest = $attributeValueService->getclosestPrev($id, $timestamp);
        $rateOfChange = $latest ? $this->calculateRateOfChange($request, $latest, $timestamp) : $this->initializeRateOfChange();

        // Prepare Data Payload
        $summary = $this->getSummary($request);
        $sqldataPayload = $this->prepareDataPayload($request, $id, $timestamp, $rateOfChange, $summary);
        $adhdataPayload = $this->prepareDataPayload($request, $id, $formattedADHTimestamp, $rateOfChange, $summary);
        // Get Access Token and Post Data to ADH
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return response()->json(['error' => 'Failed to retrieve access token'], 400);
        }

        $adhResponse = $this->postDataToADH($accessToken, $stream_id, $adhdataPayload);
        if ($adhResponse->failed()) {
            return response()->json(['error' => 'Failed to post data to ADH', 'details' => $adhResponse->body()], 400);
        }

        // Save Data Locally
        $new_value = $attributeValueService->save($sqldataPayload[0]);

        // Update closest next values
        $this->updateClosestNextValues($attributeValueService, $id, $timestamp, $request);

        // Report usage to Stripe
        $this->reportUsageToStripe($subscriptionService->getSubscriptions($request->company_id), $stripeService, $request->user_id, $request->company_id);

        return $attributeValueService->getAttributeValues($id, null, null, 'DESC');
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'company_id' => 'nullable',
            'date' => 'required|date',
            'time' => 'required|regex:/^\d{2}:\d{2}(:\d{2})?$/',
            'acetylene' => 'required|numeric',
            'ethylene' => 'required|numeric',
            'methane' => 'required|numeric',
            'ethane' => 'required|numeric',
            'hydrogen' => 'required|numeric',
            'carbon_monoxide' => 'required|numeric',
            'carbon_dioxide' => 'required|numeric',
            'oxygen' => 'nullable|numeric',
        ]);
    }

    private function calculateRateOfChange($request, $latest, $currentTimestamp)
{
    $date = new Carbon($currentTimestamp);
    $current_date = new Carbon($latest->timestamp);
    $difference = $current_date->diffInSeconds($date);
    $days = $difference / 86400;

    return [
        'acetylene_roc' => round(abs($request->acetylene - $latest->acetylene) / $days, 2),
        'ethylene_roc' => round(abs($request->ethylene - $latest->ethylene) / $days, 2),
        'methane_roc' => round(abs($request->methane - $latest->methane) / $days, 2),
        'ethane_roc' => round(abs($request->ethane - $latest->ethane) / $days, 2),
        'hydrogen_roc' => round(abs($request->hydrogen - $latest->hydrogen) / $days, 2),
    ];
}


    private function initializeRateOfChange()
    {
        return array_fill_keys(['acetylene_roc', 'ethylene_roc', 'methane_roc', 'ethane_roc', 'hydrogen_roc'], 0);
    }

    private function prepareDataPayload($request, $id, $formattedADHTimestamp, $rateOfChange, $summary)
    {
        return [[
            'asset_id' => $request->asset_id,
            'element_id' => $id,
            'company_id' => $request->company_id,
            'timestamp' => $formattedADHTimestamp,
            'acetylene' => $request->acetylene,
            'acetylene_roc' => $rateOfChange['acetylene_roc'],
            'ethylene' => $request->ethylene,
            'ethylene_roc' => $rateOfChange['ethylene_roc'],
            'methane' => $request->methane,
            'methane_roc' => $rateOfChange['methane_roc'],
            'ethane' => $request->ethane,
            'ethane_roc' => $rateOfChange['ethane_roc'],
            'hydrogen' => $request->hydrogen,
            'hydrogen_roc' => $rateOfChange['hydrogen_roc'],
            'oxygen' => $request->oxygen,
            'carbon_monoxide' => $request->carbon_monoxide,
            'carbon_dioxide' => $request->carbon_dioxide,
            'tdcg' => $summary['tdcg'],
            't1' => $summary['t1'],
            't2' => $summary['t2'],
            't3_biotemp' => $summary['t3_biotemp'],
            't3_fr' => $summary['t3_fr'],
            't3_midel' => $summary['t3_midel'],
            't3_silicon' => $summary['t3_silicon'],
            't4' => $summary['t4'],
            't5' => $summary['t5'],
            't6' => $summary['t6'],
            't7' => $summary['t7'],
            'p1' => $summary['p1'],
            'p2' => $summary['p2'],
            'iec_ratio' => $summary['iec_ratio'],
            'dornenberg' => $summary['dornenberg'],
            'carbon_ratio' => $summary['carbon_ratio'],
            'rogers_ratio' => $summary['rogers_ratio'],
            'nei' => $summary['nei'],
        ]];
    }

    private function getAccessToken()
    {
        return Cache::remember('adh_access_token', 3600, function () {
            $url = "https://uswe.datahub.connect.aveva.com/identity/connect/token";
            $client_id = env('ADH_CLIENT_ID');
            $client_secret = env('ADH_CLIENT_SECRET');

            $response = Http::asForm()->post($url, [
                'grant_type' => 'client_credentials',
                'client_id' => $client_id,
                'client_secret' => $client_secret
            ]);

            return $response->successful() ? $response['access_token'] : null;
        });
    }

    private function postDataToADH($accessToken, $stream_id, $dataPayload)
    {
        $url = 'https://auea.datahub.connect.aveva.com/api/v1/Tenants/' . env('ADH_TENANT_ID') . '/Namespaces/' . env('ADH_NAMESPACE') . '/Streams/' . $stream_id . '/Data';
        $authorizationToken = 'Bearer ' . $accessToken;

        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $authorizationToken
        ])->post($url, $dataPayload);
    }

    private function getDataToADH($accessToken, $stream_id, $startIndex, $endIndex, $element_id)
    {   
        $url = 'https://auea.datahub.connect.aveva.com/api/v1/Tenants/' . env('ADH_TENANT_ID') . '/Namespaces/' . env('ADH_NAMESPACE') . '/Streams/' . $stream_id . '/Data';

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->get($url, [
                'startIndex' => $startIndex,
                'endIndex' => $endIndex,
                'filter' => 'element_id eq ' . $element_id,
            ]);

      return $response->body();
    }

    private function updateClosestNextValues(AttributeValueService $attributeValueService, $id, $timestamp, $request)
    {
        $closestNext = $attributeValueService->getclosestNext($id, $timestamp);
        if ($closestNext) {
            $closestRateOfChange = $this->calculateRateOfChange($request, $closestNext, $timestamp);
            $attributeValueService->updateclosestNext($closestNext->id, $closestRateOfChange);
        }
    }

    private function reportUsageToStripe($subscription, StripeService $stripeService, $userId, $companyId)
    {
        if ($subscription === "advanced") {
            $stripeService->report_usage($userId, $companyId, 1);
        }
    }

}
