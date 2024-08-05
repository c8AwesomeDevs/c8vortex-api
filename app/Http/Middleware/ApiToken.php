<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\GoogleService;
use App\Services\UserService;
use App\Services\MicrosoftService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiToken
{
    protected $googleService;
    protected $microsoftService;
    protected $userService;

    public function __construct(
        GoogleService $googleService,
        MicrosoftService $microsoftService,
        UserService $userService
    ) {
        $this->googleService = $googleService;
        $this->microsoftService = $microsoftService;
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $user_id = $request->user_id;

        if (!$token || !$user_id) {
            return $this->unauthorizedResponse();
        }

        $refresh_token = $this->userService->getrefreshtoken($user_id);
        $account_type = $this->userService->getaccounttype($user_id);

        $user = $this->authenticateUser($token, $account_type);

        if (!$user) {
            $newToken = $this->refreshToken($refresh_token, $account_type);

            if ($newToken) {
                $user = $this->authenticateUser($newToken, $account_type);
                if ($user) {
                    $request->headers->set('Authorization', 'Bearer ' . $newToken);
                    // return response()->json([
                    //     'message' => 'Token refreshed successfully',
                    //     'token' => $newToken
                    // ], 200);
                } else {
                    return $this->unauthorizedResponse();
                }
            } else {
                return $this->unauthorizedResponse();
            }
        }

        $request->merge(['company_id' => $user->company_id, 'user_id' => $user->id]);
        return $next($request);
    }

    protected function authenticateUser($token, $account_type)
    {
        try {
            if ($account_type === 'google') {
                $tokenInfo = $this->googleService->verifyToken($token);
                if ($tokenInfo) {
                    return $this->userService->getUser($tokenInfo['email']);
                }
            } elseif ($account_type === 'microsoft') {
                $response = $this->microsoftService->verifyMsToken($token);
                if ($response) {
                    return $this->userService->getUser($response->getMail());
                }
            }
        } catch (\Exception $e) {
            Log::error("Authentication failed for account type $account_type: " . $e->getMessage());
        }

        return null;
    }

    protected function refreshToken($refreshToken, $account_type)
    {
        if (!$refreshToken) {
            return null;
        }

        try {
            if ($account_type === 'google') {
                $clientId = env('GOOGLE_CLIENT_ID');
                $clientSecret = env('GOOGLE_CLIENT_SECRET');
                $response = Http::post('https://oauth2.googleapis.com/token', [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type' => 'refresh_token',
                ]);

                if ($response->successful()) {
                    return $response->json()['id_token'];
                }
            } elseif ($account_type === 'microsoft') {
                // Add Microsoft token refresh logic here
            }
        } catch (\Exception $e) {
            Log::error("Token refresh failed for account type $account_type: " . $e->getMessage());
        }

        return null;
    }

    protected function unauthorizedResponse()
    {
        return response(['message' => 'Unauthorized'], 401);
    }
}
