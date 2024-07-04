<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\CompanyService;
use App\Services\ElementService;
use App\Services\TransformerService;
use App\Services\AttributeValueService;
use App\Services\UserElementService;
use App\User;
use Google\Service\Compute\Subsetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class StripeController extends Controller
{
    public function create_checkout_session()
    {
        // this function creates a Stripe-hosted link for user
        // NOTE: for now this creates the same link again and again because we are using the same product in the same price everytime
        // NOTE: if we have multiple price point for the product -> that should be conveyed to the user and depending on what price is selceted
        //      we should create a different link

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $UIDOMAIN = env('API_CLIENT_HOST');

        try {
            // $prices = \Stripe\Price::all([
            //     // retrieve lookup_key from form data POST body
            //     'lookup_keys' => [$_POST['lookup_key']],
            //     'expand' => ['data.product']
            // ]);

            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    // 'price' => $prices->data[0]->id,
                    // 'price' => 'price_1O4H81ECYNlNId8Mmtf86MC5', // flat fee + usage
                    'price' => 'price_1OCg0aECYNlNId8M2ClqCUsK', // flat fee
                    'quantity' => 1
                ], [
                    'price' => 'price_1OCg2pECYNlNId8MiKvaOHHB' // usage
                ]],
                'mode' => 'subscription',
                'success_url' => $UIDOMAIN . '/thank-you?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $UIDOMAIN . '/dashboard/assets',
                // 'subscription_data' => ['billing_cycle_anchor' => time()],
            ]);

            // header("HTTP/1.1 303 See Other");
            // header("Location: " . $checkout_session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }

        return $checkout_session->url;
    }

    public function create_customer_portal_session(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $UIDOMAIN = env('API_CLIENT_HOST') . '/dashboard/assets';

        $comapny_id = User::where('id', $request->user_id)->select('company_id')->first();
        // return $comapny_id;
        try {
            // $checkout_session = \Stripe\Checkout\Session::retrieve($_POST['session_id']);
            $customer = Subscription::where('company_id', $comapny_id['company_id'])->select('stripe_customer_id')->first();
            $return_url = $UIDOMAIN;

            // Authenticate your user.
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $customer['stripe_customer_id'],
                'return_url' => $return_url,
            ]);
            // header("HTTP/1.1 303 See Other");
            // header("Location: " . $session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // http_response_code(500);
            return response()->json(['error' => $e->getMessage()]);
        }

        return $session->url;
    }

    public function provisionAccess(Request $request,
                                     CompanyService $companyService,
                                     ElementService $elementservice,
                                     TransformerService $transformerservice,
                                     UserElementService $userelementservice,
                                     AttributeValueService $attributevalueservice)
    {
        // when a user successfully checks out Stripe will redirect the user back to the app WITH a 'checkout-session-id'
        // with that, we can do the following:

        // dig into stripe records to get the 'subscription-item-id'; this will be used for reporting of usage back to stripe later
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        // from the checkout_session get the subscription object
        $checkout_session = $stripe->checkout->sessions->retrieve(
            $request->session_id
        );
        // from the subscription object get the subscription-item-id
        $subscription = $stripe->subscriptions->retrieve(
            $checkout_session->subscription
        );

        // there will be 2 items in the subscription
        // we need to get which of those is the metered-usage
        // a metered-usage item does not have a 'quantity' property
        $stripe_subscription_item_id = "";
        foreach ($subscription->items->data as $key => $value) {
            if (!isset($subscription->items->data[$key]->quantity)) {
                $stripe_subscription_item_id = $subscription->items->data[$key]->id;
            }
        }

        // once a valid subscription-item-id is secured (subscription is recorded to stripe)
        // do the necessary changes to database
        // store the subscription-item-id and change sub type to advanced
        Subscription::where('user_id', $request->user_id)->where('company_id', $request->company_id)
            ->update([
                'subscription_type' => 'advanced',
                'stripe_subscription_id' => $subscription->id,         
                'activation_date' => date('Y-m-d', $subscription->billing_cycle_anchor),
                'expiration_date' => date('Y-m-d', $subscription->current_period_end),
                'stripe_customer_id' => $checkout_session->customer,
                'stripe_subscription_item_id' => $stripe_subscription_item_id
            ]);
        // update the limits on the number of elements a user can have; -1 for infinite
        $companyService->updateCompany($request->company_id, [
            'max_root' => -1,
            'max_sub' => -1,
            'max_tfmr' => -1,
            'max_datapoints' => -1
        ]);
        //delete all the user elements
        $userelementservice->deleteUserelement($request->user_id);
        //delete all the assets
        $elementservice->deleteAllAssets($request->company_id);
        //delete all the transformers
        $transformerservice->deleteAllTransformers($request->company_id);
        //delete all the attribute values
        $attributevalueservice->deleteAllAttributeValues($request->company_id);

        return response()->json(['message' => 'You now have Vortex Advance'], 200);
    }
}
