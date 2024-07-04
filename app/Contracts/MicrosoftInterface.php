<?php

namespace App\Contracts;

Interface MicrosoftInterface {
    public function verifyMsToken($token);
}