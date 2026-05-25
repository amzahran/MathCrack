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
        if (!session()->has('auth.2fa.user_id') || !session('auth.2fa.password_confirmed')) {
            return redirect()->route('login');
        }

        return view(theme('auth.2fa'));
    }

    public function verify(Request $request)
    {
        if (!$request->session()->has('auth.2fa.user_id') || !$request->session()->get('auth.2fa.password_confirmed')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('auth.2fa.user_id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember', 'auth.2fa.password_confirmed']);

            return redirect()->route('login');
        }

        $valid = app(Authenticator::class)->verifyGoogle2FA(
            $user->google2fa_secret,
            $request->code
        );

        if ($valid) {
            // تسجيل الدخول
            Auth::login($user, $request->session()->get('auth.2fa.remember', false));

            // مسح بيانات الجلسة المؤقتة
            $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember', 'auth.2fa.password_confirmed']);
            $request->session()->regenerate();

            return redirect()->intended(route('home', absolute: false));
        }

        return back()->with(['error' => __('l.Invalid verification code')]);
    }
}
