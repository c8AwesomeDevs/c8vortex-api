<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\GoogleService;
use App\Services\UserService;
use App\Services\MicrosoftService;
use Illuminate\Support\Facades\Http;

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
        $user_id = $request->user_id; // Ensure the refresh token is passed in the request

        $refresh_token = $this->userService->getrefreshtoken($user_id);

        if (!$token) {
            return $this->unauthorizedResponse();
        }

        $user = $this->authenticateUser($token);

        if (!$user) {
            // Try to refresh the token if authentication failed
            $newToken = $this->refreshToken($refresh_token);

            if ($newToken) {
                $user = $this->authenticateUser($newToken);
                if ($user) {
                    $request->headers->set('Authorization', 'Bearer ' . $newToken);
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

    protected function authenticateUser($token)
    {
        try {
            // Try Google authentication
            $tokenInfo = $this->googleService->verifyToken($token);
            if ($tokenInfo) {
                return $this->userService->getUser($tokenInfo['email']);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
        }

        try {
            // Try Microsoft authentication
            $response = $this->microsoftService->verifyMsToken($token);
            if ($response) {
                return $this->userService->getUser($response->getMail());
            }
        } catch (\Exception $e) {
            // Log the exception if needed
        }

        return null;
    }

    protected function refreshToken($refreshToken)
    {
        if (!$refreshToken) {
            return null;
        }

        try {
            // Google token refresh
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
        } catch (\Exception $e) {
            // Log the exception if needed
        }

        return null;
    }

    protected function unauthorizedResponse()
    {
        return response(['message' => 'Unauthorized'], 401);
    }
}
