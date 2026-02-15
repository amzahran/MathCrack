<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use App\Models\Setting;
use App\Jobs\Welcome_mailJob;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'firstname' => ['required', 'string', 'max:255'],
                    'lastname' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                    'phone' => 'string|max:20',
                    'address' => 'string|max:255',
                    'state' => 'string|max:255',
                    'zip_code' => 'string|max:15',
                    'country' => 'string|max:35',
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'password' => Hash::make($request->password),
            ]);


            $emailSetting = Setting::where('option', 'emailVerified')->value('value');
            if ($emailSetting == true) {
                event(new Registered($user));
                $message = 'Verification email has been sent.';
            } else {
                $message = '';
                $username = $request->firstname;
                $email = $request->email;
                Welcome_mailJob::dispatch($username, $email);
            }

            return response()->json([
                'status' => true,
                'message' => "Registered successfully. $message",
                'token' => $user->createToken("Mobile Token")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // التحقق من المدخلات
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            // محاولة تسجيل الدخول
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password do not match with our records.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            //--------------------------------------------------------------------------------------------------

                // حذف التوكنات القديمة إذا كنت ترغب في توليد توكن جديد في كل مرة
                // ولكن هذا يمنع من تسجيل الدخول من اكثر من جهاز في وقت واحد
                // $user->tokens()->delete();

            //--------------------------------------------------------------------------------------------------

                // تحديد حد لعدد التوكنات
                // $maxTokens = 3;
                // $tokensCount = $user->tokens()->count();

                // if ($tokensCount >= $maxTokens) {
                //     // حذف أقدم التوكنات عندما يتجاوز العدد المحدد
                //     $user->tokens()->orderBy('created_at')->limit($tokensCount - $maxTokens + 1)->delete();
                // }

            //--------------------------------------------------------------------------------------------------

                // تحديد فترة صلاحية التوكنات القديمة (على سبيل المثال، 30 يومًا)
                $expiryDate = \Carbon\Carbon::now()->subDays(30);

                // حذف التوكنات التي لم تُستخدم منذ أكثر من 30 يومًا
                $user->tokens()->where('last_used_at', '<', $expiryDate)->delete();

            //--------------------------------------------------------------------------------------------------

            // إنشاء توكن جديد
            $newToken = $user->createToken("Mobile Token")->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $newToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        // $user->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User Logged out successfully",
        ], 200);
    }
    
    public function forget(Request $request)
    {

        $validateUser = Validator::make($request->all(), ['email' => 'required|email',]);
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'This email does not match with our record',
            ], 401);
        }

        Password::sendResetLink($request->only('email'));

        return response()->json([
            "status" => true,
            "message" => "Reset link sent successfully",
        ], 200);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully',
            'data' => [$user]
        ], 200);
    }
}
