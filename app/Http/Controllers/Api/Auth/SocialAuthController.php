<?php

namespace App\Http\Controllers\api\Auth;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Jobs\Welcome_mailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialAuthController extends Controller
{
    public function handleGoogleCallback(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = Socialite::driver('google')->userFromToken($request->token);

        $dbUser = User::where('social_id', $user->id)->orWhere('email', $user->email)->first();

        if ($dbUser) {
            // Check if the email is not verified yet, then update it to mark it as verified.
            if (!$dbUser->email_verified_at) {
                $dbUser->email_verified_at = now();
                $dbUser->save();
            }
        } else {
            $name = $user->name;
            $nameParts = explode(' ', $name);
            if (count($nameParts) >= 2) {
                $firstname = $nameParts[0];
                $lastname = implode(' ', array_slice($nameParts, 1));
            } else {
                $firstname = $lastname = $name;
            }
            $dbUser = new User();
            $dbUser->firstname = $firstname;
            $dbUser->lastname = $lastname;
            $dbUser->email = $user->email;
            $dbUser->social_id = $user->id;
            $dbUser->password = bcrypt(request(Str::random())); // Set some random password
            $dbUser->email_verified_at = now();
            $dbUser->save();

            $username = $dbUser->firstname;
            $email = $dbUser->email;
            Welcome_mailJob::dispatch($username, $email);
        }
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $dbUser,
            'token' => $dbUser->createToken("Mobile Token")->plainTextToken
        ]);
    }

    public function handleFacebookCallback(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = Socialite::driver('facebook')->userFromToken($request->token);

        $dbUser = User::where('social_id', $user->id)->orWhere('email', $user->email)->first();

        if ($dbUser) {
            // Check if the email is not verified yet, then update it to mark it as verified.
            if (!$dbUser->email_verified_at) {
                $dbUser->email_verified_at = now();
                $dbUser->save();
            }
        } else {
            $name = $user->name;
            $nameParts = explode(' ', $name);
            if (count($nameParts) >= 2) {
                $firstname = $nameParts[0];
                $lastname = implode(' ', array_slice($nameParts, 1));
            } else {
                $firstname = $lastname = $name;
            }
            $dbUser = new User();
            $dbUser->firstname = $firstname;
            $dbUser->lastname = $lastname;
            $dbUser->email = $user->email;
            $dbUser->social_id = $user->id;
            $dbUser->password = bcrypt(request(Str::random())); // Set some random password
            $dbUser->email_verified_at = now();
            $dbUser->save();

            $username = $dbUser->firstname;
            $email = $dbUser->email;
            Welcome_mailJob::dispatch($username, $email);
        }
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $dbUser,
            'token' => $dbUser->createToken("Mobile Token")->plainTextToken
        ]);
    }
    public function handleTwitterCallback(Request $request)
    {
        $validator = Validator::make($request->only('token', 'secret'), [
            'token' => ['required', 'string'],
            'secret' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = Socialite::driver('twitter')->userFromTokenAndSecret($request->token, $request->secret);

        $dbUser = User::where('social_id', $user->id)->orWhere('email', $user->email)->first();

        if ($dbUser) {
            // Check if the email is not verified yet, then update it to mark it as verified.
            if (!$dbUser->email_verified_at) {
                $dbUser->email_verified_at = now();
                $dbUser->save();
            }
        } else {
            $name = $user->name;
            $nameParts = explode(' ', $name);
            if (count($nameParts) >= 2) {
                $firstname = $nameParts[0];
                $lastname = implode(' ', array_slice($nameParts, 1));
            } else {
                $firstname = $lastname = $name;
            }
            $dbUser = new User();
            $dbUser->firstname = $firstname;
            $dbUser->lastname = $lastname;
            $dbUser->email = $user->email;
            $dbUser->social_id = $user->id;
            $dbUser->password = bcrypt(request(Str::random())); // Set some random password
            $dbUser->email_verified_at = now();
            $dbUser->save();

            $username = $dbUser->firstname;
            $email = $dbUser->email;
            Welcome_mailJob::dispatch($username, $email);
        }
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $dbUser,
            'token' => $dbUser->createToken("Mobile Token")->plainTextToken
        ]);
    }
}