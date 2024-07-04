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

        $user = $graph->createRequest("GET", "/me")
                      ->setReturnType(User::class)
                      ->execute();
    
        return $user;
    }
}