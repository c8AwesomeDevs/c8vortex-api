<?php

namespace App\Http\Controllers;

use App\Mail\UserAccountApproved;
use App\Models\Element;
use App\Models\UserElement;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\UserElementService;
use App\Services\ElementService;
use App\Services\TransformerService;
use App\Services\CompanyService;
use App\Services\AttributeValueService;
use App\Services\SubscriptionService;
use App\User;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function getUsers(Request $request, UserService $userService)
    {
        return $userService->getCompanyUsers($request->company_id);
    }

    public function getUserElements(
        $user_id,
        Request $request,
        UserElementService $userElementService,
        ElementService $elementService
    ) {

        $response = [
            'elements' => $elementService->generateHierarchyAll($request->company_id),
            'user_elements' => $userElementService->getElementIds($user_id)
        ];

        return response()->json($response);
    }

    public function updateUserElements($user_id, Request $request, UserElementService $userElementService, ElementService $elementService, UserService $userService)
    {
        if ($userService->isUserAnAdmin($request->user_id) == false) return response()->json(["message" => "No, you are not allowed to do that."], 401);

        $curr_user_elems = UserElement::where('user_id', $user_id)->select('element_id')->get();
        $curr_array = $curr_user_elems->map(function ($e) {
            return intval($e['element_id']);
        })->toArray();

        // create 2arrays of what to add and what to remove
        // in this instance we cant figure out what to change and do the change at the same time
        $add = [];
        foreach ($request->assets as $key => $value) {
            // array of what to add
            if (!in_array(intval($value), $curr_array)) {
                array_push($add, $value);
            }
        }

        $rem = [];
        foreach ($curr_array as $key => $value) {
            // array of what to remove
            if (!in_array(intval($value), $request->assets)) {
                array_push($rem, $value);
            }
        }

        // do the changes from the arrays
        foreach ($add as $key => $value) {
            $userElementService->add($user_id, $value);
        }
        foreach ($rem as $key => $value) {
            $userElementService->delete($user_id, $value);
        }

        return response()->json([
            // 'company_elems' => $curr_array,
            // "updated" => $request->assets,
            "added" => $add,
            "removed" => $rem,
        ], 201);
    }

    public function updateAccountStatus(Request $request)
    {
        // validate
        $validated = $request->validate([
            'user' => 'required|email',
            'action' => 'required|string',
        ]);

        if ($request->has(['user', 'action'])) {
            switch ($request->query('action')) {
                case 'approve':
                    User::where('email', $request->query('user'))->update(['account_status' => 'active']);
                    // Send email to user that his account has been approved
                    try {
                        Mail::to($validated['user'])->queue(new UserAccountApproved($validated));
                    } catch (\Throwable $th) {
                        return $th;
                    }
                    break;
                case 'decline':
                    User::where('email', $request->query('user'))->update(['account_status' => 'declined']);
                    break;
                case 'disable':
                    User::where('email', $request->query('user'))->update(['account_status' => 'inactive']);
                    break;
                default:
                    # code...
                    break;
            }

            return response()->json(['message' => 'User Status successfully ' . $request->query('action') . 'd'], 201);
        } else {
            return response()->json(['message' => 'User Status Updated Unsuccessful'], 500);
        }
    }

    public function tutorialDone(Request $request, UserService $userService, $user_id)
    {
        // return $user_id;
        $user = $userService->getFullDetails($user_id);
        if ($user) {
            $is_updated = User::where('id', $user->id)->update(['is_new' => false]);
            if ($is_updated) {
                return response()->json(["message" => "User updated successfully", "is_updated" => $is_updated], 200);
            } else {
                return response()->json(["message" => "Failed to update user"], 500);
            }
        } else {
            return response()->json(["message" => "User not found"], 404);
        }
    }

    public function addUser(Request $request, 
                            UserService $userService,
                            UserElementService $userelementservice,
                            ElementService $elementservice,
                            TransformerService $transformerservice,
                            AttributeValueService $attributevalueservice, 
                            CompanyService $companyservice,
                            SubscriptionService $subscriptionservice){

                        // Validate the email address
        $email = $request->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Invalid email address'], 400);
        }

        // Check if the email is already registered
        $emailCheckResult = $userService->registeredEmailwithSubs($request->email);

        if ($emailCheckResult) {
            $subscription = $subscriptionservice->getSubscriptions($emailCheckResult->company_id);
            $subscriptionType = $subscription[0]['subscription_type'];

            if($subscriptionType === 'demo'){

              
                    $deleteUserElement = $userelementservice->deleteUserelement($emailCheckResult->id);
                    $deleteCompany = $companyservice->deleteCompany($emailCheckResult->company_id);
                    $deleteUser = $userService->deleteUser($emailCheckResult->company_id);
                    $deleteSubscription = $subscriptionservice->deleteSubscription($emailCheckResult->company_id);
                    $deleteallAssets = $elementservice->deleteAllAssets($emailCheckResult->company_id);
                    $deleteAllTransformers = $transformerservice->deleteAllTransformers($emailCheckResult->company_id);
                    $deleteAllAttributeValues = $attributevalueservice->deleteAllAttributeValues($emailCheckResult->company_id);
    
                    if($deleteSubscription && $deleteUser){
                   
                            $userName = $request->firstName . ' ' . $request->lastName;
                            $data = [
                                'company_id' => $request->company_id,
                                'name' => $userName,
                                'email' => $request->email,
                                'account_type' => $request->account_type,
                                'account_level' => 'company_user',
                                'account_status' => 'active',
                            ];
        
                            $newUser = $userService->saveUser($data);
        
                            return response()->json([
                                'user' => $newUser,
                            ]);
                    }
               
            }else{
                    $user = $userService->getcompanyuser($emailCheckResult->id);
                    $accountlevel = $user[0]['account_level'];
                if($accountlevel === 'company_admin'){
                    return response()->json(['error' => 'Email is already in use as company admin.'], 400);
                }else{
                    return response()->json(['error' => 'User already added as company user.'], 400);
                }

            }


        }else{

            $userName = $request->firstName . ' ' . $request->lastName;
            $data = [
                'company_id' => $request->company_id,
                'name' => $userName,
                'email' => $request->email,
                'account_type' => $request->account_type,
                'account_level' => 'company_user',
                'account_status' => 'active',
            ];

            $newUser = $userService->saveUser($data);

            return response()->json([
                'user' => $newUser,
            ]);
        }
    }

    public function deleteUser(Request $request)
    {
        $userElement_deleted = UserElement::where('user_id', $request->delete_user)->delete();

        $user_deleted = User::where('id', $request->delete_user)->delete();

        return response()->json([
            'userElement_deleted' => $userElement_deleted,
            'user_deleted' => $user_deleted
        ], 200);
    }

    public function updateUser(Request $request, UserService $userService)
    {
        if ($userService->isUserAnAdmin($request->user_id) == false) return response()->json(["message" => "No, youre not allowed to do that."], 401);

        $validated = $request->validate([
            "id" => "required",
            "email" => "required|email",
            "name" => "required|string",
        ]);

        $user_id = $validated['id'];
        unset($validated['id']);

        $updated = User::where('id', $user_id)->update($validated);

        if ($updated == 1) {
            return response()->json(['message' => "Update Success"], 200);
        } else {
            return response()->json(['message' => "Update Failed"], 400);
        }
    }
}
