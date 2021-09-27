<?php

namespace App\Services;

use Exception;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * Resolve user by provider credentials.
     *
     * @param string $provider
     * @param string $accessToken
     * @return Authenticatable|null
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;
        try {
            // validate social token from provider
            $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        /**
         * check if access token from social provider is valid
         * check user is exists otherwise create new user
         * form social account service
         */
        if ($providerUser) {
            return (new SocialAccountService())->findOrCreate($providerUser, $provider);
        }
        // if access token not valid return error message
        return response()->json([
            'success' => false,
            'message' => 'Something wrong, login attempt failed',
        ]);
    }
}
