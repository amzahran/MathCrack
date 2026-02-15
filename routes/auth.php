<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SimpleLoginController;

Route::middleware('guest')->group(function () {

    // GET: عرض صفحة التسجيل
    Route::get('/login', [SimpleLoginController::class, 'show'])->name('login');
    
    // POST: معالجة التسجيل - غير الاسم ليصبح 'login' فقط
    Route::post('/login', [SimpleLoginController::class, 'login'])->name('login'); // ⬅️ غير هنا

    // تسجيل حساب جديد
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    
    // نسيت كلمة المرور
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgoutPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

// تسجيل الخروج
Route::post('/logout', [SimpleLoginController::class, 'logout'])->name('logout')->middleware('auth');