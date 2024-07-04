<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Payments\CreditCardPaymentService;
use App\Services\SubscriptionService;
use App\Services\UserService;
use App\Services\CompanyService;
use App\Services\ElementService;
use App\Services\TransformerService;
use App\Services\AttributeValueService;
use App\Services\UserElementService;
use App\Http\Resources\TransformerResource;

class PaymentController extends Controller
{
    public function check(CreditCardPaymentService $ccPaymentService) {
        print_r($ccPaymentService);die;
    }

    public function success(Request $request, 
        CreditCardPaymentService $ccPaymentService,
        SubscriptionService $subscriptionService,
        UserService $userService,
        CompanyService $companyservice,
        ElementService $elementservice,
        TransformerService $transformerservice,
        UserElementService $userelementservice,
        AttributeValueService $attributevalueservice) {

        $checkout_session = $ccPaymentService->validateCheckOutSession($request->session_id);
        $subscription = $subscriptionService->updateSubscription($checkout_session->client_reference_id, $checkout_session);
        $userelementservice->deleteUserelement($subscription->user_id);
        $elementservice->deleteAllAssets($subscription->company_id);
        $transformerservice->deleteAllTransformers($subscription->company_id);
        $attributevalueservice->deleteAllAttributeValues($subscription->company_id);
        $companyservice->updateCompany($subscription->company_id, ['max_root' => -1,
                                                                    'max_sub' => -1,
                                                                    'max_tfmr' => -1,
                                                                    'max_datapoints' => -1]);

        if(is_object($subscription)) {
            if($request->r == 'thank-you') {
                return redirect(env('API_CLIENT_HOST') . '/thank-you');
            }
            elseif($request->r == 'subscription') {
                return redirect(env('API_CLIENT_HOST') . '/dashboard/subscription');
            }
        }
    }
    public function renew(Request $request,
        CreditCardPaymentService $ccPaymentService,
        SubscriptionService $subscriptionService) {
            $checkout_session = $ccPaymentService->validateCheckOutSession($request->session_id);
        
            $subscription = $subscriptionService->renew($checkout_session->client_reference_id, $checkout_session);

        if(is_object($subscription)) {
            return redirect(env('API_CLIENT_HOST') . '/dashboard/subscription');
        }
    }
}
