<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Level;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{
    public function create()
    {
        $countries = Country::all();
        $levels = Level::all();
        $canRegister = Setting::where('option', 'can_any_register')->value('value');

        if ($canRegister != 1) {
            return redirect()->route('login')->with('error', 'Registration is not allowed');
        }

        return view(theme('auth.register'), ["countries" => $countries, "levels" => $levels]);
    }

    public function store(Request $request): RedirectResponse
    {
        // تحقق أولي من رقم الهاتف
        $phone = str_replace(' ', '', (string) $request->phone);
        $phone = '+' . (string) $request->phone_code . $phone;
        
        // التحقق من تكرار رقم الهاتف مبكراً
        $existingPhone = User::where('phone', $phone)->first();
        if ($existingPhone) {
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['phone' => 'Phone number is already registered. Please use another number']);
        }

        // التحقق من تكرار الإيميل
        $existingEmail = User::where('email', $request->email)->first();
        if ($existingEmail) {
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Email is already registered. Please use another email']);
        }

        $request->validate([
            'firstname' => ['required', 'string', 'max:30'],
            'lastname' => ['required', 'string', 'max:60'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:20'],
            'phone_code' => ['required', 'string', 'max:6'],
            'level_id' => ['required', 'exists:levels,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:30'],
            'zip_code' => ['nullable', 'string', 'max:25'],
            'country' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['nullable', 'string'],
        ], [
            'password.confirmed' => 'Password and confirm password do not match',
            'phone_code.required' => 'Phone code is required',
        ]);

        if ($request->filled('fax_number')) {
            abort(403, 'Bot detected');
        }

        $recaptchaEnabled = Setting::where('option', 'recaptcha')->value('value') ?? 0;

        if ((int) $recaptchaEnabled === 1) {
            $recaptchaResponse = $request->input('g-recaptcha-response');

            if (!$recaptchaResponse) {
                return redirect()->back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors([
                        'g-recaptcha-response' => __('reCAPTCHA is required'),
                    ]);
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha.secret'),
                'response' => $recaptchaResponse,
                'remoteip' => \Symfony\Component\HttpFoundation\IpUtils::anonymize($request->ip()),
            ]);

            $result = $response->json();

            if (!$response->successful() || empty($result['success']) || $result['success'] !== true) {
                return redirect()->back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors([
                        'g-recaptcha-response' => __('reCAPTCHA Error'),
                    ]);
            }
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $phone,
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'password' => Hash::make($request->password),
            'level_id' => $request->level_id,
        ]);

        // ===== لا ترسل أي بريد إلكتروني =====
        // تم حذف كود إرسال البريد بالكامل

        // ===== تسجيل الدخول =====
        $request->session()->regenerate();
        Auth::login($user);
        
        // ===== التوجيه الفوري =====
        return redirect()->route('home')->with('success', 'Registration successful!');
    }
}