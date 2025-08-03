<?php

namespace App\Http\Controllers\App\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver("google")
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver("google")->user();
        $user = User::where('email', $googleUser->email)->first();
        if ($user) {
            // email already used, what we have todo? just link it with google
            $user->google_id = $googleUser->id;
            $user->save();
        } else {
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->id],
                [
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Str::password(12),
                    'email_verified_at' => now(),
                    'active' => 1,
                ]
            );
        }

        Auth::login($user);

        return redirect(route('app.dashboard'))
            ->with('success', __('messages.registration-logged-in-success'));
    }
}
