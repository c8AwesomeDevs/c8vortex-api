<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\CompanyService;
use App\Models\Company;
use App\User;
use App\Services\SubscriptionService;

class RegistrationController extends Controller
{
    public function register(Request $request, UserService $userService, CompanyService $companyService, SubscriptionService $subscriptionService)
    {
        // Validate the email address
        $email = $request->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Invalid email address'], 400);
        }

        // Check if the email is already registered
        if ($userService->isEmailRegistered($request->email)) {
            return response()->json(['error' => 'Email is already registered'], 400);
        } else {
            // Extract the domain from the email
            $emailParts = explode('@', $request->email);
            $emaildomain = end($emailParts);

            // Save the company first and get the newly created company object
            $newCompany = $companyService->registerCompany([
                'company_name' => $request->company_name,
                'country' => $request->country,
                'domain' => $emaildomain,
                'industry' => $request->industry,
                'hear_aboutus' => $request->hear_aboutus,
            ]);

            // Get the company ID based on the company name
            $companyId = $newCompany->id;

            // Use the retrieved company ID for user registration
            $newUser = $userService->registerUser([
                'company_id' => $companyId,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'account_level' => 'company_admin',
                // 'account_status' => "pending",
                'account_status' => "active",
            ]);

            $subscriptionService->subscribeForDemo($newUser->id, $newCompany->id);

            // Return both the company data and user data in an array
            return [
                'company' => $newCompany,
                'user' => $newUser,
            ];
        }
    }
}
