<?php
namespace App\Services\Payments;

use App\Contracts\Payments\CreditCardPaymentInterface;
use \Stripe\StripeClient;
class CreditCardPaymentService implements CreditCardPaymentInterface 
{
    protected $stripe;
    
    public function __construct() {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function authenticate() {
        return $stripe;
    }

    public function validateCheckOutSession($session_id) {
        return $this->stripe->checkout->sessions->retrieve($session_id, []);
    } 
}