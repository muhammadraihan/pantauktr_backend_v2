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
        // dd($provider, $accessToken);
        $providerUser = null;
        try {
            if ($provider == "apple") {
                config()->set('services.apple.client_secret', $accessToken);
                $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
                // dd($providerUser);
                // $applePubKeySet = Http::get('https://appleid.apple.com/auth/keys');
                // $keySet = $applePubKeySet->json();
                // $validateToken = JWT::decode($accessToken, JWK::parseKeySet($keySet), ['RS256']);
                // $user = [
                //     'id' => $validateToken->sub,
                //     'email' => $validateToken->email,
                //     'name' => '',
                //     'avatar' => '',
                // ];
                // $encodeUser = json_encode($user);
                // $providerUser =  json_decode($encodeUser);
                // dd($validateToken, $providerUser);
            } else {
                $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
                // dd($providerUser);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        // dd($providerUser);
        if ($providerUser) {
            return (new SocialAccountService())->findOrCreate($providerUser, $provider);
        }
        return response()->json([
            'success' => false,
            'message' => 'Something wrong, login attempt failed',
        ]);
    }
}
