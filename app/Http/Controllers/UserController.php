<?php

namespace App\Http\Controllers;

use App\Aggregates\UserAggregate;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\AuthorResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $uuid = $this->generateUuid();

        UserAggregate::retrieve($uuid)
            ->register($request->name, $request->surname, $request->nickname, $request->email)
            ->persist();

        $user = User::where('uuid', $uuid)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user, true);

        return new ProfileResource($user);
    }

    public function login(UserLoginRequest $request)
    {
        if (Auth::attempt($request->only(['email', 'password']), true)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            return new ProfileResource(Auth::user());
        }

        throw ValidationException::withMessages([
            'password' => ['Ni pravo geslo.'],
        ]);
    }

    public function update(UpdateUserRequest $request)
    {
        UserAggregate::retrieve($request->user()->uuid)
            ->update($request->name, $request->surname, $request->nickname)
            ->persist();

        return new ProfileResource($request->user()->refresh());
    }

    public function show(Request $request)
    {
        return new ProfileResource($request->user());
    }

    public function stats(Request $request)
    {
        $stats = DB::table('user_counters')->where('user_uuid', '=', $request->user()->uuid)->first();

        if ($stats) {
            $stats = $stats->number_of_unread_notifications;
        } else {
            $stats = 0;
        }

        return response()->json(['data' => [
            'numberOfUnreadNotifications' => (int)$stats,
        ]]);
    }

    public function verify(Request $request)
    {
        if (!$request->hasValidSignature()) {
            throw new AuthorizationException;
        }

        $user = User::where('uuid', $request->get('id'))->first();

        if (!$user) {
            throw new AuthorizationException;
        }


        if (!hash_equals((string)$request->get('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect(env('FRONTEND_URL') . '/profil');
    }

    public function reverify(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response(['data' => null]);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $response = Password::broker()->sendResetLink(
            ['email' => $request->get('email')]
        );

        return response(['data' => null]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = Password::broker()->reset(
            $request->only('email', 'password', 'token'), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? response()->json(['data' => null], 200)
            : throw ValidationException::withMessages([
                'email' => ['Nismo uspeli resetirati gesla, saj je veljavnost povezave iz emaila potekla.'],
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json();
    }
}
