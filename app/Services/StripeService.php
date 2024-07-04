<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Subscription;
use App\User;
use Error;
use Ramsey\Uuid\Uuid;

class StripeService
{
    public function isSubscriptionActive($user_id)
    {
        // this function checks with Stripe API if a certain subcription is active

        // get the company of the user
        $user_company = User::where('id', $user_id)->select('company_id')->first();

        // get the subscription_id of the company
        $company_subs = Subscription::where('company_id', $user_company['company_id'])->select('subscription_type', 'stripe_subscription_id')->first();

        // only do the retrieve on Stripe API when subscription_type is not demo
        if ($company_subs['subscription_type'] != 'demo') {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $subscription = $stripe->subscriptions->retrieve(
                $company_subs['stripe_subscription_id']
            );
            if ($subscription->status == 'active') {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function report_usage($user_id, $company_id, $usage_quantity = 1)
    {
        // REPORT USAGE TO STRIPE EVERYTIME USER SAVES A DATAPOINT

        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // This code can be run on an interval (for example, every 24 hours) for each active
        // metered subscription.

        // You need to write some of your own business logic before creating the
        // usage record. Pull a record of a customer from your database
        // and extract the customer's Stripe Subscription Item ID and
        // usage for the day. If you aren't storing subscription item IDs,
        // you can retrieve the subscription and check for subscription items
        // https://stripe.com/docs/api/subscriptions/object#subscription_object-items.
        // $subscription_item_id = 'si_OsA4YTAgR8iPA8';

        // The usage number you've been keeping track of in your database for
        // the last 24 hours.
        // $usage_quantity = 100;

        $subscription_item_id = Subscription::where('user_id', $user_id)->where('company_id', $company_id)->select('stripe_subscription_item_id')->first();

        $date = date_create();
        $timestamp = date_timestamp_get($date);

        // The idempotency key allows you to retry this usage record call if it fails.
        $idempotency_key = Uuid::uuid4()->toString();

        try {
            \Stripe\SubscriptionItem::createUsageRecord(
                $subscription_item_id['stripe_subscription_item_id'],
                [
                    'quantity' => $usage_quantity,
                    'timestamp' => $timestamp,
                    'action' => 'set',
                ],
                [
                    'idempotency_key' => $idempotency_key,
                ]
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return "Usage report failed for item ID $subscription_item_id with idempotency key
            $idempotency_key: {$e->getMessage()}";
            // return false;
        }

        return true;
    }
}
