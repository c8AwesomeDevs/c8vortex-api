<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// access these routes to see how the emails look like
Route::get('/newUserMail', function () {
    return view('emails.newUserMail')->with([
        "name" => "John Doe",
        "email" => "john.doe@companydomain.com",
        "unique_id" => "vasldkhrfklhhjdfclsndf",
        "account_type" => "google",
        "account_level" => "company_admin",
        "company_id" => "1",
    ]);
});
Route::get('/accountApprovalMail', function () {
    return view('emails.requestApprovalMail')->with([
        "name" => "asd",
        "email" => "email",
        "phone_number" => "phone_number",
        "company_id" => "company_id",
        "approver" => "approver",
        "linkApprove" => "linkApprove",
        "linkDecline" => "linkDecline",
    ]);
});
Route::get('/newAccountAccomodate', function () {
    return view('emails.newUserAccomodated')->with([
        "email" => "email",
        "action" => "approasdve",
    ]);
});
