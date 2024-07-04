<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ADHService;
use Illuminate\Support\Facades\Http;

class ADHController extends Controller
{
    //
    public function addAdh(Request $request, ADHService $adhService)
    {
        $tenants = env('ADH_TENANT_ID');
        $namespace = env('ADH_NAMESPACE');

        $url = "https://uswe.datahub.connect.aveva.com/identity/connect/token";
        $client_id = env('ADH_CLIENT_ID');
        $client_secret = env('ADH_CLIENT_SECRET');
        
        $ADHrefreshToken = Http::asForm()->post($url, [
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret
        ]);
    
        $adhResponse = null;
        if ($ADHrefreshToken->successful()) {
            $accessToken = $ADHrefreshToken["access_token"];
            $streamId = $request->stream_id;
        
            $url = 'https://auea.datahub.connect.aveva.com/api/v1/Tenants/' . $tenants . '/Namespaces/' . $namespace . '/Streams/' . $streamId;
            $authorizationToken = 'Bearer ' . $accessToken;
        
            $adhResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $authorizationToken
            ])->post($url, [
                'Id' => $streamId,
                'Name' => $streamId,
                'Description' => 'C8 vortex manual log',
                'TypeId' => 'C8_DGA_Gasses'
            ]);
        }
        $data = [
            'company_id' => $request->company_id,
            'name' => $streamId,
            'stream_id' =>$streamId,
            'descriptions' => 'C8 vortex manual log',
            'type' => "C8_DGA_Gasses"

        ];
        $new_adh = $adhService->addAdh($data);
        $response = [
            'new_adh' => $new_adh,
            'adh_config' => $adhResponse ? $adhResponse->json() : null
        ];

        return response()->json($response);
    }

    public function getAdh(Request $request, ADHService $adhService){
        
        $adhConfig = $adhService->getAdh($request->company_id);
        
        return $adhConfig;
    }
    public function updateAdh(Request $request, $id, ADHService $adhService)
    {
        $data = [
            'tenants' => $request->tenants,
            'namespace' => $request->namespace,
        ];
        $updated_adh = $adhService->updateAdh($data, $id);

        if ($updated_adh) {
            return response()->json($updated_adh);
        } else {
            return response()->json(['error' => 'Configuration not found'], 404);
        }
    }
}
