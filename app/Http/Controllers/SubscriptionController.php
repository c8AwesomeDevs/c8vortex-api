<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubscriptionService;
class SubscriptionController extends Controller
{
    public function getSubscriptions(Request $request, SubscriptionService $subscriptionService) {
        return $subscriptionService->getSubscriptions($request->company_id);
    }

    public function checkCurrentSubscription(Request $request, SubscriptionService $subscriptionService) {
        if($request->subscription_type == 'demo') {
            return $subscriptionService->checkCurrentDemoSubscription($request->company_id);
        }

        return $subscriptionService->checkCurrentSubscription($request->company_id, $request->type);
    }

    public function subscribeForDemo(Request $request, SubscriptionService $subscriptionService) {
        return $subscriptionService->subscribeForDemo($request->company_id, $request->user_id);
    }
}
