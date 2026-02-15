<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

$middleware = ['auth:sanctum'];
$settings = app('view')->shared('settings');
$emailVerified = $settings['emailVerified'] ?? false;
if ($emailVerified == true) {
    $middleware = ['auth:sanctum', 'verified'];
}


Route::get("/", function () {return response()->json(['You are in index now', app()->getLocale()]); });


Route::group([
    "middleware" => $middleware
], function(){
    Route::post("home", function () {return response()->json('You are logged in And you in home now'); });
    Route::post("profile", [AuthController::class, "profile"]);


    Route::post("logout", [AuthController::class, "logout"]);

    require __DIR__ . '/api/users.php';
});


require __DIR__ . '/api/auth.php';
