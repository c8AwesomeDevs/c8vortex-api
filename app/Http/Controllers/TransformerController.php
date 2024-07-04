<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransformerService;
use App\Services\ADHService;
use Illuminate\Support\Facades\Http;

class TransformerController extends Controller
{
    public function saveTransformer(Request $request, TransformerService $transformerService, ADHService $adhService, $id)
    {
        // return $request->stream_id;
        // $adh_config = $adhService->getAdh($request->company_id);
    
        // $tenants = $adh_config[0]['tenants'];
        // $namespace = $adh_config[0]['namespace'];
        
        // $url = "https://uswe.datahub.connect.aveva.com/identity/connect/token";
        // $client_id = env('ADH_CLIENT_ID');
        // $client_secret = env('ADH_CLIENT_SECRET');
        
        // $ADHrefreshToken = Http::asForm()->post($url, [
        //     'grant_type' => 'client_credentials',
        //     'client_id' => $client_id,
        //     'client_secret' => $client_secret
        // ]);
    
        // $adhResponse = null;
        // if ($ADHrefreshToken->successful()) {
        //     $accessToken = $ADHrefreshToken["access_token"];
        //     $streamId = $request->stream_id;
        
        //     $url = 'https://auea.datahub.connect.aveva.com/api/v1/Tenants/' . $tenants . '/Namespaces/' . $namespace . '/Streams/' . $streamId;
        //     $authorizationToken = 'Bearer ' . $accessToken;
        
        //     $adhResponse = Http::withHeaders([
        //         'Content-Type' => 'application/json',
        //         'Authorization' => $authorizationToken
        //     ])->post($url, [
        //         'Id' => $streamId,
        //         'Name' => $streamId,
        //         'Description' => 'C8 vortex manual log',
        //         'TypeId' => 'C8_DGA'
        //     ]);
        // }
    
        $data = [
            'element_id' => $id,
            'company_id' => $request->company_id,
            'startup_date' => $request->startup_date,
            'manufacturer' => $request->manufacturer,
            'type' => $request->type,
            'construction_year' => $request->construction_year,
            'age_band' => $request->age_band,
            'line_capacity' => $request->line_capacity,
            'winding_voltage' => $request->winding_voltage,
            'asset_desc' => $request->asset_desc,
            'address' => $request->address,
            'country_manufacturer' => $request->country_manufacturer,
            'serial_no' => $request->serial_no,
            'model_no' => $request->model_no,
            'volt_capacity' => $request->volt_capacity
        ];
        $new_transformers = $transformerService->save($data);
    
        $response = [
            'new_transformers' => $new_transformers,
            // 'adh_response' => $adhResponse ? $adhResponse->json() : null
        ];
    
        return response()->json($response);
    }
    

    public function updateTransformer(Request $request, TransformerService $transformerService, $id)
    {
        $data = [
            'element_id' => $id,
            'company_id' => $request->company_id,
            'startup_date' => $request->startup_date,
            'manufacturer' => $request->manufacturer,
            'type' => $request->type,
            'construction_year' => $request->construction_year,
            'age_band' => $request->age_band,
            'line_capacity' => $request->line_capacity,
            'winding_voltage' => $request->winding_voltage,
            'asset_desc' => $request->asset_desc,
            'address' => $request->address,
            'country_manufacturer' => $request->country_manufacturer,
            'serial_no' => $request->serial_no,
            'model_no' => $request->model_no,
            'volt_capacity' => $request->volt_capacity
        ];
        $new_transformers = $transformerService->update($id, $data);

        return response()->json($new_transformers);
    }

    public function getTransformersDetails(Request $request, TransformerService $transformerService, $element_id)
    {
        $transformer_details = $transformerService->getTransformersDetails($element_id);
        return response()->json($transformer_details);
    }
}
