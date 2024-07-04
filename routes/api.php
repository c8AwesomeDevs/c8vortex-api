<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/tests', function () {
    return response()->json([
        'Hi' => 'Hello World',
        'Hello' => 'Hi World',
    ]);
});

Route::post('create/check', 'InfluxController@createCheck');
Route::post('create/notif-rule', 'InfluxController@createnotifRule');
Route::get('/test', 'GoogleController@test');
Route::post('/events', 'InfluxController@saveEvents');
Route::post('/register', 'RegistrationController@register');

Route::post('/google/auth', 'GoogleController@authenticate');
Route::post('/microsoft/auth', 'MicrosoftController@authenticate');
Route::get('/elements/{id}/path', 'ElementController@getElementPath');
Route::get('/payments/test', 'PaymentController@check');
Route::get('/payment/success', 'PaymentController@success');
Route::get('/payment/renew', 'PaymentController@renew');
Route::post('/subscriptions/check', 'StripeController@create_customer_portal_session');

// get a hash --- this should not be moved to prod --- if absolutely needed, modify for better security
Route::get('/getHash', 'SuperUserController@getUsableHash');
// had to create new middleware for approvers
// cant put it in 'apitoken' because the access is not in the dashboard; its in an email
// cant leave it just laying outside because anybody can just access it; its quite critical
Route::middleware('apikey')->group(function () {
    // account activators
    Route::get('/approver/useractivation', 'UserController@updateAccountStatus');
    // get mappings for piping in data from influx
    Route::get('/getMappings', 'SuperUserController@getMappings');
    // pipe-in DGA datapoints from influx
    Route::post('/influxpipe/DGA/{id}', 'AttributeValueController@saveAttributeValue');
    // pipe-in H2S datapoints from influx
    Route::post('/influxpipe/H2S/{id}', 'TimeseriesController@store');

    // update max datapoints --- this should not be moved to prod --- if absolutely needed, modify for better security
    Route::post('/updateUserMaxDatapoints', 'SuperUserController@updateUserMaxDatapoints');
    // update subscription expiration date --- this should not be moved to prod --- if absolutely needed, modify for better security
    Route::post('/updateUserSubExpiration', 'SuperUserController@updateUserSubExpiration');
    Route::post('/updateUserSubType', 'SuperUserController@updateSubbscriptiontype');
    // artisan migrate and refresh
    // artisan migrate and refresh --- this should not be moved to prod --- if absolutely needed, modify for better security
    Route::post('/run-artisan-command', 'SuperUserController@refreshDatabase');
});

Route::middleware('apitoken')->group(function () {
    Route::get('/profile', 'ProfileController@getFullProfile');
    Route::put('/profile', 'ProfileController@updateProfile');
    Route::get('/hierarchy', 'ElementController@getHierarchy');
    Route::get('/hierarchyAll', 'ElementController@getHierarchyAll');

    Route::get('/elements', 'ElementController@getElements');
    Route::get('/elements/{id}', 'ElementController@getElementDetails');
    Route::get('/elementscount/{id}', 'ElementController@getelementsCount');
    Route::put('/elements/{id}', 'ElementController@updateElementDetails');
    Route::delete('/elements/{id}', 'ElementController@deleteElement');
    Route::post('/elements/{id}', 'ElementController@addElementDetails');

    Route::get('/elements/{id}/attributes', 'AttributeController@getAttributes');
    Route::post('/elements/{id}/attributes', 'AttributeController@createAttributes');

    Route::get('/elements/{id}/overview', 'ElementController@getTransformersOverview');
    // Route::post('/elements/{id}/path', 'ElementController@getElementPath');

    Route::get('/elements/{id}/attribute-values', 'AttributeValueController@getValueLength');
    Route::get('/elements/{id}/attribute-values', 'AttributeValueController@get');
    Route::post('/elements/{id}/attribute-values', 'AttributeValueController@saveAttributeValue');


    Route::post('/elements/{id}/transformers', 'TransformerController@saveTransformer');
    Route::put('/elements/{id}/transformers', 'TransformerController@updateTransformer');
    Route::post('/elements/{id}/comments', 'CommentsController@saveComments');
    Route::get('/elements/{id}/comments', 'CommentsController@getComments');
    Route::put('/comments/{id}', 'CommentsController@updateComments');
    Route::delete('/comments/{id}', 'CommentsController@deleteComments');
    // Route::get('/elements/{id}/transformer-details', 'TransformerController@getTransformersDetails');

    Route::get('/subscriptions', 'SubscriptionController@getSubscriptions');
    Route::get('/subscriptions/check', 'SubscriptionController@checkCurrentSubscription');
    Route::get('/subscriptions/manage', 'StripeController@create_customer_portal_session');
    Route::get('/checkout', 'StripeController@create_checkout_session');
    Route::post('/provisionAccess', 'StripeController@provisionAccess');

    Route::get('/users', 'UserController@getUsers');
    Route::post('/addUser', 'UserController@addUser');
    Route::patch('/updateUser', 'UserController@updateUser');
    Route::post('/deleteUser', 'UserController@deleteUser');
    Route::get('/users/{user_id}/elements', 'UserController@getUserElements');
    Route::post('/users/{user_id}/elements', 'UserController@updateUserElements');
    Route::get('/users/{user_id}/tutorial-done', 'UserController@tutorialDone');

    //Demo Subscription
    Route::post('/demo-subscription', 'SubscriptionController@subscribeForDemo');

    // Feedback and Suupport
    Route::post('/concerns', 'CommentsController@accomodateFeedbackAndSupport');

    Route::get('/influx/buckets', 'InfluxController@getCompanyBuckets');
    Route::post('/influx/saveBucket', 'InfluxController@saveBucket');
    Route::get('/influx/sensorTypes', 'InfluxController@getSensorTypes');
    Route::post('/influx/sensorType', 'InfluxController@saveSensorType');
    Route::post('/influx/testMapping', 'InfluxController@testMapping');
    Route::post('/influx/saveMapping', 'InfluxController@saveMapping');
    Route::post('/influx/query', 'InfluxController@getTimeseries');

    //adh
    Route::post('/adh-config/save', 'ADHController@addAdh');
    Route::get('/adh-config', 'ADHController@getAdh');
    Route::put('/adh-config/update/{id}', 'ADHController@updateAdh');
});


// Route::post('/influx/query', 'InfluxController@getTimeseries');

// mock API endpoint for svg generation
// Route::post('/duvalsIllustrations', 'SVGController@assembleSVGs');
// for testing symbols
Route::post('/elements/{id}/attribute-values--testing', 'AttributeValueController@saveAttributeValue');
