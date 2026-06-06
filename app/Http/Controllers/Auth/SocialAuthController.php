<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider's OAuth authentication page.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirectToProvider(string $provider): RedirectResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider and authenticate.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        try {
            // Retrieve user data from the provider
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            // Log the actual exception details
            \Log::error("Social auth callback failed for provider {$provider}: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // Redirect back to login with error details if verification failed
            return redirect()->route('login')->withErrors([
                'email' => __('Đăng nhập qua mạng xã hội không thành công. Vui lòng thử lại.'),
            ]);
        }

        // Validate that we have an email address
        $email = $socialUser->getEmail();
        if (!$email) {
            return redirect()->route('login')->withErrors([
                'email' => __('Không thể lấy địa chỉ email từ tài khoản mạng xã hội của bạn.'),
            ]);
        }

        // Authenticate/Link in a transaction to prevent inconsistent states
        $user = DB::transaction(function () use ($socialUser, $provider, $email) {
            // Check if the social account exists
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_uid', $socialUser->getId())
                ->first();

            if ($socialAccount) {
                // Update access/refresh tokens in case they changed
                $socialAccount->update([
                    'access_token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                    'token_expires_at' => isset($socialUser->expiresIn) ? now()->addSeconds($socialUser->expiresIn) : null,
                ]);
                return $socialAccount->user;
            }

            // Find user by email or create a new one
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Register a new customer
                $user = User::create([
                    'full_name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Lumiere Member',
                    'email' => $email,
                    'password_hash' => Hash::make(Str::random(24)),
                    'role_id' => 6, // customer role
                ]);
            }

            // Link the social account
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_uid' => $socialUser->getId(),
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken ?? null,
                'token_expires_at' => isset($socialUser->expiresIn) ? now()->addSeconds($socialUser->expiresIn) : null,
            ]);

            return $user;
        });

        // Log the user in
        Auth::login($user, true);

        // Redirect to original intended path or fall back to home
        return redirect()->intended(route('home'));
    }
}
