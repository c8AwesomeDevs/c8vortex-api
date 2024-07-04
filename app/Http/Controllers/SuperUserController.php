<?php

namespace App\Http\Controllers;

use Artisan;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\Bucket;
use App\Models\Sensor;
use Illuminate\Http\Request;
use App\Services\UserService;

class SuperUserController extends Controller
{
    public function getUsableHash(Request $request)
    {
        return hash('murmur3c', env('APPROVERS_API_KEY') . $request->query('action') . 'salt and pepper', false);
    }

    // http://localhost:8000/api/updateUserMaxDatapoints?action=updateUser&k=45f722e0b875135cea1eda026840219e
    public function updateUserMaxDatapoints(Request $request, UserService $userService)
    {
        // validate
        $validated = $request->validate([
            'user' => 'required|email',
            'max_datapoints' => 'required|numeric',
            'max_root' => 'required|numeric',
            'max_sub' => 'required|numeric',
            'max_tfmr' => 'required|numeric',
        ]);

        // find user
        $user = $userService->getUser($validated['user']);
        if ($user->account_level !== 'company_admin') return response()->json(['message' => "User indicated is not not a Company Admin"], 401);
        if ($user) {
            // update users company
            $company = Company::where('id', $user['company_id'])->update(['max_datapoints' => $validated['max_datapoints'],
                                                                            'max_root' => $validated['max_root'],
                                                                            'max_sub' => $validated['max_sub'],
                                                                            'max_tfmr' => $validated['max_tfmr']]);

            if ($company) {
                return response()->json(['message' => "User updated successfully"], 200);
            } else {
                // if update failed
                return response()->json(['message' => "Failed updating record"], 500);
            }
        } else {
            return response()->json(['message' => "User not found"], 404);
        }

        return response()->json(['message' => "Internal Error"], 500);
    }


    //http://localhost:8000/public/api/updateUserSubType?action=update-subs-type&k=29099895b6b48494b998ad0ae499ec6c
    public function updateSubbscriptiontype(Request $request, UserService $userService){

        // validate
        $validated = $request->validate([
            'user' => 'required|email',
            'subscription_type' => 'required|string',
        ]);

        // find user
        $user = $userService->getUser($validated['user']);
        if ($user->account_level !== 'company_admin') return response()->json(['message' => "User indicated is not not a Company Admin"], 401);
        if ($user) {
            // update users company
            $sub = Subscription::where('user_id', $user['id'])->update(['subscription_type' => $validated['subscription_type']]);

            if ($sub) {
                return response()->json(['message' => "User Subscription type updated successfully"], 200);
            } else {
                // if update failed
                return response()->json(['message' => "Failed updating record"], 500);
            }
        } else {
            return response()->json(['message' => "User not found"], 404);
        }

        return response()->json(['message' => "Internal Error"], 500);
    }

    // http://localhost:8000/api/updateUserSubExpiration?action=updateExpiration&k=3e969ccff3828bc7828a1dbbb3e23d8d
    public function updateUserSubExpiration(Request $request, UserService $userService)
    {
        // validate
        $validated = $request->validate([
            'user' => 'required|email',
            'expiration_date' => 'required|date',
        ]);

        // find user
        $user = $userService->getUser($validated['user']);
        if ($user->account_level !== 'company_admin') return response()->json(['message' => "User indicated is not not a Company Admin"], 401);
        if ($user) {
            // update users company
            $sub = Subscription::where('user_id', $user['id'])->update(['expiration_date' => $validated['expiration_date']]);

            if ($sub) {
                return response()->json(['message' => "User Subscription expiration date updated successfully"], 200);
            } else {
                // if update failed
                return response()->json(['message' => "Failed updating record"], 500);
            }
        } else {
            return response()->json(['message' => "User not found"], 404);
        }

        return response()->json(['message' => "Internal Error"], 500);
    }

    // http://localhost:8000/api/run-artisan-command?action=migrate-and-refresh&k=e7354aadebe79dc9590c2cda5f079625
    public function refreshDatabase(Request $request)
    {
        set_time_limit(120); // Set the maximum execution time to 2 minutes

        $validated = $request->validate([
            'pass-phrase' => 'required|string',
            'command' => 'required|string'
        ]);

        if ($validated['pass-phrase'] == "subtext-coke-spotted") {
            $allowedCommands = ['db:seed', 'migrate', 'cache:clear', 'migrate:refresh', 'migrate:refresh --seed', 'db:seed --class=H2ScanSeeder']; // Define a whitelist of allowed commands

            if (in_array($validated['command'], $allowedCommands)) {
                // Perform additional validation or sanitation if necessary
                // Optimize the command execution, use batching, or asynchronous processing if applicable
                Artisan::call($validated['command']);

                return response()->json(['message' => $validated['command'] . " command run successfully"], 200);
            } else {
                return response()->json(['message' => "Invalid command"], 400);
            }
        } else {
            return response()->json(['message' => "If you're a hacker, please don't erase our database. Please?"], 500);
        }
    }

    public function getMappings(Request $request)
    {
        // $request->query("company")
        $buckets = Sensor::join('buckets', function ($join) use ($request) {
            $join->on('bucket_id', '=', 'buckets.id')->where('company_id', $request->query("company"));
        })
            ->join('sensor_type', 'sensor_type_id', '=', 'sensor_type.id')
            ->join('transformers', 'transformer_id', '=', 'transformers.id')
            ->select('sensors.id', '_measurement', 'org_id', 'influx_bucket_id', 'influx_bucket_name', 'token_read', 'token_write', 'company_id', 'sensor_type.type', 'transformers.element_id')
            ->get();

        foreach ($buckets as $key => $value) {
            $filters =  Sensor::join('filters', 'sensors.id', '=', 'filters.sensor_id')
                ->select('key', 'value')
                ->where('sensors.id', $value['id'])
                ->get();

            $tags = Sensor::join('tags', 'sensors.id', '=', 'tags.sensor_id')
                ->select('tags.id', '_field', 'tag_name')
                ->where('sensors.id', $value['id'])
                ->get();

            $buckets[$key]['filters'] = $filters;
            $buckets[$key]['tags'] = $tags;
        }

        return $buckets;
    }
}
