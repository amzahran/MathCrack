<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Web\Auth\NewPasswordController;
use App\Http\Controllers\api\Auth\SocialAuthController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forget-password', [AuthController::class, 'forget']);
Route::post('reset-password/{token}', [NewPasswordController::class, 'create']);



// social media auth
    // google auth
    Route::post('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    // facebook auth
    Route::post('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
    // twitter auth
    Route::post('/auth/twitter/callback', [SocialAuthController::class, 'handleTwitterCallback']);
