<?php

namespace App\Http\Controllers\Web\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Level;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ProfileController extends Controller {
    public function index(Request $request)
    {
        $countries = Country::all();
        $apis = DB::table('personal_access_tokens')->where('tokenable_id', Auth::id())->get();
        $sessions = Auth::user()->authentications;
        $levels = Level::all();
        return view('themes/default/back/profile', ['countries' => $countries, 'sessions' => $sessions, 'apis'=> $apis, 'levels'=> $levels]);
    }

    public function update(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $input = $request->all();

        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'required|string|max:20',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'level_id' => 'nullable|exists:levels,id',
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $phone = str_replace(' ', '', $input['phone']);
        $phone = '+'.$input['phone_code'].$phone;

        $user = User::find(Auth::id());

        $user->firstname = $input['firstname'];
        $user->lastname = $input['lastname'];
        // $user->email = $input['email'];
        $user->phone = $phone;
        $user->address = $input['address'];
        $user->city = $input['city'];
        $user->state = $input['state'];
        $user->zip_code = $input['zip_code'];
        $user->country = $input['country'];
        $user->level_id = $input['level_id'];
        $user->save();

        return redirect()->back()->with('success', __('l.Profile updated successfully'));
    }

    public function uploadphoto(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:800',
        ]);

        $user = User::find(Auth::id());

        if ($user->photo !=  asset('images/usersProfile/profile.png')) {
            $path = public_path('images/usersProfile/' . basename($user->photo));
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $filename = upload_to_public($request->photo, 'images/usersProfile');

        $user->image = $filename;
        $user->save();

        return redirect()->back()->with('success', __('l.Image updated successfully'));
    }

    public function updatepassword(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->back()->with('success', __('l.Password updated successfully'))->with('showPage2', true);
        } catch (ValidationException $e) {
            // إعادة توجيه في حالة وجود خطأ في التحقق من البيانات المدخلة
            return redirect()->back()->withErrors($e->errors())->with('showPage2', true);
        }
    }

    public function delete(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $user = User::find(Auth::id());

        if (basename($user->photo) != 'profile.png') {
            $path = public_path('images/users_profile/' . basename($user->photo));
            if (file_exists($path)) {
                unlink($path);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function apiCreate(Request $request)
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $user = User::find(Auth::id());
        $token = $user->createToken("$request->name")->plainTextToken;

        return redirect()->back()->with(['done' => __('l.API created successfully'), 'token' => $token])->with('showPage2', true);
    }

    public function apiDelete(Request $request): RedirectResponse {
        $encryptedName = $request->input('name');
        $apiName = decrypt($encryptedName);

        DB::table('personal_access_tokens')->where('tokenable_id', Auth::id())->where('name', $apiName)->delete();

        return redirect()->back()->with('success', __('l.API deleted successfully'))->with('showPage2', true);
    }

    public function show2faForm()
    {
        if (auth()->user()->hasRole('demo')) {
            return view('themes.default.back.permission-denied');
        }

        $user = Auth::user();

        if (empty($user->google2fa_secret)) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();

            // إنشاء رابط Google Authenticator
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // إعدادات QR Code مع زيادة الحجم
            $options = new QROptions([
                'version'    => 7,
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel'   => QRCode::ECC_L,
                'scale'      => 5,
                'addQuietZone' => true,
                'quietZoneSize' => 2
            ]);

            // إنشاء QR code
            $qrcode = new QRCode($options);
            $qrImage = $qrcode->render($qrCodeUrl);

            session([
                '2fa_secret' => $secret,
                'qrImage' => $qrImage
            ]);

            return redirect()->route('dashboard.profile')
                ->with('showPage2', true);
        }

        return redirect()->route('dashboard.profile')->with('showPage2', true);
    }

    public function enable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = session('2fa_secret');

        if ($google2fa->verifyKey($secret, $request->code)) {
            $user->google2fa_secret = $secret;
            $user->save();

            session()->forget('2fa_secret');
            return redirect()->route('dashboard.profile')
                ->with('success', __('l.Two-factor authentication enabled successfully'))
                ->with('showPage2', true);
        }

        return redirect()->route('dashboard.profile')
            ->with('error', __('l.Invalid verification code'))
            ->with('2fa_action', 'enable')
            ->with('showPage2', true);
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            $user->google2fa_secret = null;
            $user->save();

            return redirect()->route('dashboard.profile')
                ->with('success', __('l.Two-factor authentication disabled successfully'))
                ->with('showPage2', true);
        }

        return redirect()->route('dashboard.profile')
            ->with('error', __('l.Invalid verification code'))
            ->with('2fa_action', 'disable')
            ->with('showPage2', true);
    }
}