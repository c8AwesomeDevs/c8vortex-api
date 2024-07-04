<?php

namespace App\Services;

use App\Models\ADHConfig;
use App\Contracts\ADHInterface;
use Illuminate\Support\Facades\Http;

class ADHService implements ADHInterface

{
    public function getAdh($company_id){

        $adh_config = ADHConfig::where('company_id', $company_id)->get();

        return $adh_config;

    }
    public function addAdh($data) {
        $new_adh = ADHConfig::create($data);
        return $new_adh;
    }

    public function updateAdh($data, $id)
    {
        // Logic to update ADH configuration
        $adhConfig = ADHConfig::find($id);
        if ($adhConfig) {
            $adhConfig->update($data);
            return $adhConfig;
        }
        return null; // Or handle the case where the configuration is not found
    }

    public function refreshToken($data){
        // Get Access Token
        $url = "https://uswe.datahub.connect.aveva.com/identity/connect/token";
        $client_id = env('ADH_CLIENT_ID');
        $client_secret = env('ADH_CLIENT_SECRET');

        $ADHrefreshToken = Http::asForm()->post($url, [
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret
        ]);
        
    }
}