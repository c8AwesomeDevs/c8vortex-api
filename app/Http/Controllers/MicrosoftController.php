<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MicrosoftService;
use App\Services\UserService;
use App\Services\CompanyService;
use App\Services\StripeService;
use App\User;
// use Microsoft\Graph\Model\User;
class MicrosoftController extends Controller
{
    public function authenticate(Request $request, MicrosoftService $microsoftService, UserService $userService, CompanyService $companyService, StripeService $stripeService)
    {
        $response = $microsoftService->verifyMsToken($request->token);

        // return response()->json([
        //     'response' => $response,
        // ]);

        if ($response) {

            // Check if the email exists in the database
            $user = User::where('email', $response->getMail())->first();

            if ($user) {
                // Check the account status
                if ($user->account_status === 'active') {
                    // check with stripe api if the the subscription is active
                    if ($stripeService->isSubscriptionActive($user['id'])) {
                        $user_data = [
                            'account_type' => 'microsoft',
                            'refresh_token' => $request->refresh_token
                        ];
                        // Email is registered in the database and the account is active
                        // Perform necessary actions
                        $updatedUser = $userService->updateUser($user->id, $user_data);

                        // $user = $userService->registerUser($user_data);
                        // Pass the updated user's ID to getFullDetails method
                        $user_details = $userService->getFullDetails($user->id);
                        return response()->json([
                            'user' => $user_details,
                            'token' => $request->token
                        ]);
                    } else {
                        return response()->json(['error' => 'Company Subscription is not in an active state.'], 400);
                    }
                } else {
                    // Account is pending, prevent login
                    return response()->json(['error' => 'Account needs approval. Cannot login.'], 403);
                }
            } else {
                // Email is not registered in the database
                // Perform necessary actions (e.g., show an error message)
                return response()->json(['error' => 'Email is not registered'], 404);
            }
        } else {
            return response()->json(['Access Denied'], 403);
        }
    }
}
