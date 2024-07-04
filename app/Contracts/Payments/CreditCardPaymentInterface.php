<?php

namespace App\Contracts\Payments;

interface CreditCardPaymentInterface 
{
    public function authenticate();
    public function validateCheckOutSession($session_id);
}