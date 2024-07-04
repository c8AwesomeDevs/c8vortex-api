<?php

namespace App\Services;

use App\User;
use App\Models\Subscription;
use App\Contracts\UserInterface;
use App\Mail\AccountApproval;
use App\Mail\UserAccountCreation;
use App\Models\Company;
use Illuminate\Support\Facades\Mail;

class UserService implements UserInterface
{
    public function getUser($email)
    {
        $user = User::where('email', $email);
        return $user->first();
    }

    public function saveUser($data)
    {
        $new_user = User::create($data);

        return $new_user;
    }

    public function deleteUser($company_id){

        $deleteUser = User::where('company_id', $company_id)->delete();

        return $deleteUser;
    }

    public function isEmailRegistered($email)
    {
        // Use Eloquent ORM to check if any user with the provided email exists in the users table
        $registered = User::where('email', $email)->exists();

        return $registered;
    }

    public function registeredEmailwithSubs($email)
    {
        // Use Eloquent ORM to check if any user with the provided email exists in the users table
        $user = User::where('email', $email)->first();

        return $user;

        // if ($user) {
        //     // If the user exists, also retrieve their subscription type
        //     $subscription = Subscription::where('company_id', $user->company_id)
        //         ->where('expiration_date', '>=', now()) // Check for active subscriptions
        //         ->first();

        //     if ($subscription) {
        //         // If a subscription is found, return the user and subscription type
        //         return $subscription;
        //     }else{
        //         // If no subscription is found, return the user with no subscription type
        //         $registered = User::where('email', $email)->exists();

        //         return $registered;
        //     }

        
        // }
    }

    public function getcompanyuser($id){
        $users = User::where('id', $id);
        return $users->get();
    }

    public function getCompanyUsers($company_id)
    {
        $users = User::where('company_id', $company_id);
        return $users->get();
    }

    public function registerUser($data)
    {
        if (is_object($this->getUser($data['email']))) {
            return $this->getUser($data['email']);
        } else {
            // Retrieve the company name based on the company ID provided in the $data array.
            $company = Company::find($data['company_id']); // Assuming you have a 'company_id' key in the $data array.

            if (!$company) {
                // Handle the case where the company is not found.
                return response()->json(['error' => 'Company not found'], 404);
            }

            // Add the company name to the $data array.
            $data['company_name'] = $company->company_name;

            $user = User::create($data);



            try {
                // When we create a new user we must notify that user and the approving team
                // so the new acoount can be approved
                Mail::to($data['email'])->queue(new UserAccountCreation($data));

                // approvers needs to confirm newly created users so we also send them emails
                // $approvers = ['c8vortexsupport@calibr8systems.com'];
                $approvers = ['c8vortexsupport@calibr8systems.com'];
                foreach ($approvers as $approver) {
                    $data['approver'] = $approver;
                    Mail::to($approver)->queue(new AccountApproval($data));
                }
            } catch (\Throwable $th) {
                //
            }

            return $user;
        }
    }

    public function getFullDetails($id)
    {
        $user = User::with('company')->where('id', $id)->first();
        $subscriptions = Subscription::where('company_id', $user->company_id)
            ->where('expiration_date', '>=', date('Y-m-d'))
            ->first();

        $user->subscription = $subscriptions;
        // $response = array_merge($user, $subscriptions);

        return $user;
    }

    public function updateUser($id, $data)
    {
        $user = User::where('id', $id)->update($data);

        return $user;
    }

    public function isUserAnAdmin($user_id)
    {
        $user_account_level = User::where('id', $user_id)->select('account_level')->get();

        if ($user_account_level[0]['account_level'] == "company_admin") {
            return true;
        } else {
            return false;
        }
    }
}
