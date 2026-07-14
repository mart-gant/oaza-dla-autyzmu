<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Provider not supported.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Provider not supported.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Wystąpił problem podczas autoryzacji z ' . ucfirst($provider) . '.'
            ]);
        }

        // Find existing user by social ID
        $user = User::where($provider . '_id', $socialUser->getId())->first();

        if ($user) {
            // Log in the user
            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        }

        // If user with the same email already exists, link their social account
        if ($socialUser->getEmail()) {
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($existingUser) {
                $existingUser->update([
                    $provider . '_id' => $socialUser->getId()
                ]);
                
                Auth::login($existingUser, true);
                return redirect()->intended('/dashboard');
            }
        }

        // Create a new user
        $newUser = User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
            'email' => $socialUser->getEmail(),
            $provider . '_id' => $socialUser->getId(),
            'email_verified_at' => now(), // Social providers verify emails
        ]);

        Auth::login($newUser, true);

        return redirect()->intended('/dashboard');
    }
}
