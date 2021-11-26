<?php

namespace App\Services;

use Exception;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class SocialUserResolver implements SocialUserResolverInterface
{
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;
        try {
            if ($provider == "apple") {
                config()->set('services.apple.client_secret', $accessToken);
                $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
            } else {
                $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        if ($providerUser) {
            return (new SocialAccountService())->findOrCreate($providerUser, $provider);
        }
        return response()->json([
            'success' => false,
            'message' => 'Something wrong, login attempt failed',
        ]);
    }
}
