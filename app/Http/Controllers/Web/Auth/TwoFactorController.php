<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function challenge()
    {
        if (!session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        return view(theme('auth.2fa'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('auth.2fa.user_id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $valid = app(Authenticator::class)->verifyGoogle2FA(
            $user->google2fa_secret,
            $request->code
        );

        if ($valid) {
            // تسجيل الدخول
            Auth::login($user, session('auth.2fa.remember', false));

            // مسح بيانات الجلسة المؤقتة
            session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);

            return redirect()->intended(route('home', absolute: false));
        }

        return back()->with(['error' => __('l.Invalid verification code')]);
    }
}
