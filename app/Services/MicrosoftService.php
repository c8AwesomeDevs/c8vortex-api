<?php

namespace App\Services;

use App\Contracts\MicrosoftInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User;

class MicrosoftService implements MicrosoftInterface
{
    public function verifyMsToken($token) {
        $graph = new Graph();
        $graph->setAccessToken($token);

        try {
            $user = $graph->createRequest("GET", "/me")
                          ->setReturnType(User::class)
                          ->execute();

            return $user;
        } catch (\Exception $e) {
            // Handle exception, possibly log error or return a specific message
            return ['error' => $e->getMessage()];
        }
    }
}
