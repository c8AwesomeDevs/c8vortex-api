<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\GoogleService;
use App\Services\UserService;
use App\Services\MicrosoftService; // Import the MicrosoftService


class ApiToken
{
    // protected $microsoftService;

    // public function __construct(MicrosoftInterface $microsoftService)
    // {
    //     $this->microsoftService = $microsoftService;
    // }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $googleService = new GoogleService();
        $tokenInfo = $googleService->verifyToken($request->bearerToken());
        if ($tokenInfo) {
            $userservice = new UserService();
            $user = $userservice->getUser($tokenInfo['email']);
            if ($user) {
                $request->merge(['company_id' => $user->company_id, 'user_id' => $user->id]);
                return $next($request);
            }
        } 
        else {
            $microsoftService = new MicrosoftService(); // Use the injected MicrosoftService
            $response = $microsoftService->verifyMsToken($request->bearerToken());

            if ($response) {
                $userservice = new UserService();
                $user = $userservice->getUser($response->getMail()); // Use the appropriate method to retrieve the email from the Microsoft User object
                if ($user) {
                    $request->merge(['company_id' => $user->company_id, 'user_id' => $user->id]);
                    return $next($request);
                }
            }
        }
        
        return response(['Unauthorized'], 401);
    }
}
