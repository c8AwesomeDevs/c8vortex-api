<?php

namespace App\Contracts;

interface SubscriptionInterface
{
    public function subscribe($ref_id, $data);
    public function getSubscriptions($company_id);
}