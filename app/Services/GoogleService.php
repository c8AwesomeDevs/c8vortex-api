<?php

namespace App\Services;

use App\Contracts\GoogleInterface;
use Google_Client;

class GoogleService implements GoogleInterface
{
    protected $clientId;
    public function __construct() {
        $this->clientId = env('GOOGLE_CLIENT_ID');
    }
    public function verifyToken($token) {
        $client = new Google_Client(['client_id' => $this->clientId]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($token);
        
        return $payload;
    }
}