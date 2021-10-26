<?php

namespace App\Services;

use App\Models\Pelapor;
use App\Models\LinkedSocialAccount;
use Illuminate\Support\Facades\Auth;
use laravel\Socialite\Two\User as ProviderUser;

class SocialAccountService
{
    /**
     * Find or create user instance by provider user instance and provider name.
     *
     * @param ProviderUser $providerUser
     * @param String $provider
     * @return Pelapor
     */
    public function findOrCreate(ProviderUser $providerUser, String $provider): Pelapor
    {
        /**
         * Setup auth provider instance
         * This will treat "pelapors-api" guard as session
         * To fix multiple auth guard for using passport.
         */
        config(['auth.guards.pelapors-api.driver' => 'session']);
        // check linked social account to existing user
        $linkedSocialAccount = LinkedSocialAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())->first();
        // if match return user
        if (!is_null($linkedSocialAccount)) {
            // loggedin pelapor
            Auth::guard('pelapors-api')->login($linkedSocialAccount->pelapor);
            return $linkedSocialAccount->pelapor;
        } else {
            // check if user exist
            $pelapor = Pelapor::where('email', $providerUser->getEmail())->first();
            // if not exist create one
            if (is_null($pelapor)) {
                $name = $providerUser->getName();
                $parts = explode(" ", $name);
                if (count($parts) > 1) {
                    $lastname = array_pop($parts);
                    $firstname = implode(" ", $parts);
                } else {
                    $firstname = $name;
                    $lastname = " ";
                }
                $pelapor = new Pelapor;
                $pelapor->firstname = $firstname;
                $pelapor->lastname = $lastname;
                $pelapor->email = $providerUser->getEmail();
                $pelapor->avatar =  $providerUser->getAvatar();
                $pelapor->provider = $provider;
                $pelapor->save();
                // logged in pelapor
                Auth::guard('pelapors-api')->login($pelapor);
            }
            // save linked social accounts info
            $pelapor->LinkedSocialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);
            // logged in pelapor
            Auth::guard('pelapors-api')->login($pelapor);
            return $pelapor;
        }
    }
}
