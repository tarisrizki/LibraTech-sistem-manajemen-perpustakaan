<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }

        $email = $googleUser->getEmail();
        if (! $email) {
            return redirect()->route('login')->withErrors(['email' => 'Akun Google tidak memiliki email.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'password' => Str::random(32),
                'role' => UserRole::Member,
                'email_verified_at' => now(),
            ]);
        } elseif (empty($user->google_id) && ! empty($googleUser->getId())) {
            // backfill google_id if column exists
            try {
                $user->forceFill(['google_id' => $googleUser->getId()])->save();
            } catch (\Throwable) {
                // column may not exist yet — ignore
            }
        }

        // store google_id/avatar when available
        if (! empty($googleUser->getId()) || $googleUser->getAvatar()) {
            try {
                $payload = [];
                if ($googleUser->getId()) {
                    $payload['google_id'] = $googleUser->getId();
                }
                if ($googleUser->getAvatar()) {
                    $payload['google_avatar'] = $googleUser->getAvatar();
                }
                if ($payload) {
                    $user->forceFill($payload)->save();
                }
            } catch (\Throwable) {
                // columns optional
            }
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        // verify isAdmin guard sync — redirect admin to admin area, else intended
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.books.index'));
        }

        return redirect()->intended(route('catalog.index'));
    }
}
