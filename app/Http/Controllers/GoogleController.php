<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleService;
use App\Services\UserService;
use App\Services\CompanyService;
use App\Services\StripeService;
use App\User;
use Illuminate\Support\Facades\Http;
use Google_Client;
class GoogleController extends Controller
{

    protected $clientId;
    public function __construct() {
        $this->clientId = env('GOOGLE_CLIENT_ID');
    }
    public function verifyToken($token) {
        $client = new Google_Client(['client_id' => $this->clientId]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($token);
        
        return $payload;
    }
    public function authenticate(Request $request, GoogleService $googleService, UserService $userService, CompanyService $companyService, StripeService $stripeService)
    {
        $client = new Google_Client(['client_id' => $this->clientId]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($request->token);
        $response = $payload;

        return response()->json([
            'response' => $response,
            'id_token' => $request->token,
        ]);
        if ($response) {
            // Check if the email exists in the database
            $user = User::where('email', $response['email'])->first();

            if ($user) {
                // Check the account status
                if ($user->account_status === 'active') {
                    // check with stripe api if the the subscription is active
                    if ($stripeService->isSubscriptionActive($user['id'])) {
                        $user_data = [
                            'account_type' => 'google',
                            'refresh_token' => $request->refresh_token,
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
                        return response()->json([
                            'user_id' => $user->id,
                            'error' => 'Company Subscription is not in an active state.'
                        ], 400);
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
            // return response()->json(['Access Denied'], 403);
            return response(['error' => 'Access Denied'], 403);
        }
    }

    public function test(){
        return response()->json([
            'Hi' => 'Hello World',
            'Hello' => 'Hi World',
        ]);
    }


    public function redirectToGoogle()
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        $redirectUri = env('GOOGLE_REDIRECT_URI');
        $scope = urlencode(env('GOOGLE_SCOPE', 'email profile')); // Ensure scopes are URL encoded
        $responseType = 'code';
        $accessType = 'offline';
        $prompt = 'consent'; // Explicitly request consent
    
        $url = "https://accounts.google.com/o/oauth2/auth?" .
               "client_id={$clientId}&" .
               "redirect_uri={$redirectUri}&" .
               "response_type={$responseType}&" .
               "scope={$scope}&" .
               "access_type={$accessType}&" .
               "prompt={$prompt}";
    
        return redirect($url);
    }
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
    
        if ($code) {
            $clientId = env('GOOGLE_CLIENT_ID');
            $clientSecret = env('GOOGLE_CLIENT_SECRET');
            $redirectUri = env('GOOGLE_REDIRECT_URI');
    
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);
    
            $data = $response->json();
            $accessToken = $data['access_token'];
            $idToken = $data['id_token'];
            $refreshToken = $data['refresh_token'];
    
            // Redirect to Vue.js frontend with tokens
            $frontendRedirectUri = 'http://localhost:8080/';
            return redirect("{$frontendRedirectUri}?access_token={$accessToken}&id_token={$idToken}&refresh_token={$refreshToken}");
        }
    
        return redirect('/login')->withErrors(['error' => 'Failed to authenticate with Google.']);
    }
}
