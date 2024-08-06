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
      
      
        $user = $this->authenticateUser($token);


        $request->merge(['company_id' => $user->company_id, 'user_id' => $user->id]);
        return $next($request);
    }

    protected function authenticateUser($token)
    {
        try {
        
                $tokenInfo = $this->googleService->verifyToken($token);
                if ($tokenInfo) {
                    return $this->userService->getUser($tokenInfo['email']);
                }
       
                $response = $this->microsoftService->verifyMsToken($token);
                if ($response) {
                    return $this->userService->getUser($response->getMail());
                }
      
        } catch (\Exception $e) {
        
        }

        return null;
    }


    protected function unauthorizedResponse()
    {
        return response(['message' => 'Unauthorized'], 401);
    }
}
