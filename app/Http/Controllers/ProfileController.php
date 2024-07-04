<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfileService;
use App\Services\UserService;

class ProfileController extends Controller
{
    public function getFullProfile(Request $request, UserService $userService) {
        return response()->json(
            [
                'token' => $request->bearerToken(),
                'user' => $userService->getFullDetails($request->user_id)
            ]
        );
    }

    public function updateProfile(Request $request, ProfileService $profileService) {
        $update = $profileService->update($request->company);

        return response()->json($update);
    }
}
