<?php

namespace App\Contracts;

interface GoogleInterface
{
    public function verifyToken($token);
}