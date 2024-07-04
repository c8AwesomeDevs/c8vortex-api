<?php

namespace App\Services;

use DB;
use App\Contracts\SubscriptionInterface;
use App\Models\Subscription;
use App\User;
use App\Models\Company;

class SubscriptionService implements SubscriptionInterface
{
    protected function checkSubscription($reference_id)
    {
        $subscription = Subscription::where('reference_id', $reference_id);

        return $subscription->first();
    }

    public function deleteSubscription($company_id){

        $deleteSubscription = Subscription::where('company_id', $company_id)->delete();

        return $deleteSubscription;
    }

    public function getSubscriptions($company_id)
    {
        $subscriptions = Subscription::where('company_id', $company_id);
        return $subscriptions->get();
    }
    
    public function updateSubscription($ref_id, $subscription_data){
        $ref = explode(':', $ref_id);
        $type = $ref[1];
        $id = $ref[0];
        $user = User::find($id);
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+30 days'));
        

        // Find the subscription by user ID
        $subscription = Subscription::where('user_id', $user->id)->first();  
      
        if (!$subscription) {
            // Subscription not found for the given user ID
            return null;
        }
        
    
        // Update the subscription data
        $subscription->update([
            'subscription_type' => $type,
            'reference_id' => $subscription_data->id,
            'activation_date' => $start,
            'expiration_date' => $end,
            // Add other fields that you want to update
        ]);
    
        return $subscription;

    }

    public function subscribe($ref_id, $data)
    {
        $current_subscription = $this->checkSubscription($data->id);
        if (is_object($current_subscription)) {
            return $current_subscription;
        }

        $ref = explode(':', $ref_id);
        $id = $ref[0];
        $type = $ref[1];

        $user = User::find($id);

        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+30 days'));

        $data = [
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'subscription_type' => $type,
            'subtotal' => $data->amount_subtotal,
            'total' => $data->amount_total,
            'currency' => $data->currency,
            'payment_status' => $data->payment_status,
            'activation_date' => $start,
            'expiration_date' => $end,
            'reference_id' => $data->id,
            'activated' => 1
        ];

        $subscription = Subscription::create($data);

        return $subscription;
    }

    public function renew($ref_id, $data)
    {
        $current_subscription = Subscription::find($ref_id);
        $start = date('Y-m-d', strtotime('+1 days', strtotime($current_subscription->expiration_date)));
        $end = date('Y-m-d', strtotime('+365 days', strtotime($start)));

        $data = [
            'user_id' => $current_subscription->id,
            'company_id' => $current_subscription->company_id,
            'subscription_type' => $current_subscription->subscription_type,
            'subtotal' => $data->amount_subtotal,
            'total' => $data->amount_total,
            'currency' => $data->currency,
            'payment_status' => $data->payment_status,
            'activation_date' => $start,
            'expiration_date' => $end,
            'reference_id' => $data->id,
            'activated' => 0
        ];

        $subscription = Subscription::create($data);

        return $subscription;
    }

    public function checkCurrentSubscription($company_id, $type)
    {
        $subscription = Subscription::where('company_id', $company_id)
            ->where('subscription_type', $type)
            ->where('expiration_date', '>=', date('Y-m-d'))
            ->orderBy('expiration_date', 'DESC');

        return $subscription->first();
    }

    public function checkCurrentDemoSubscription($company_id)
    {
        $subscription = Subscription::where('company_id', $company_id)
            ->where('subscription_type', 'demo');

        return $subscription->first();
    }

    public function subscribeForDemo($user_id, $company_id)
    {
        $user = User::find($user_id);

        // Get the company
        $company = Company::find($company_id);

        // Update company data
        $company->max_root = 1;
        $company->max_sub = 1;
        $company->max_tfmr = 1;
        $company->max_datapoints = 3;
        $company->save();

        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+30 days'));
        $reference_id = uniqid(); // Generate random reference ID
        $data = [
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'subscription_type' => 'demo',
            'subtotal' => 0,
            'total' => 0,
            'currency' => 'USD',
            'payment_status' => 'paid',
            'activation_date' => $start,
            'expiration_date' => $end,
            'reference_id' => $reference_id,
            'activated' => 1,
        ];

        $subscription = Subscription::create($data);



        return $subscription;
    }

    
}
