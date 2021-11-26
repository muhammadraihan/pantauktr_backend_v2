<?php

namespace App\Services;

use App\Models\Pelapor;
use App\Models\LinkedSocialAccount;
use Illuminate\Support\Facades\Auth;
use laravel\Socialite\Two\User as ProviderUser;

class SocialAccountService
{
    public function findOrCreate(ProviderUser $providerUser, String $provider): Pelapor
    {
        config(['auth.guards.pelapors-api.driver' => 'session']);

        $linkedSocialAccount = LinkedSocialAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())->first();

        if (!is_null($linkedSocialAccount)) {

            Auth::guard('pelapors-api')->login($linkedSocialAccount->pelapor);
            return $linkedSocialAccount->pelapor;
        } else {
            $pelapor = Pelapor::where('email', $providerUser->getEmail())->first();
            if (is_null($pelapor)) {
                if ($provider == 'apple') {
                    $email = $providerUser->getEmail();
                    $extract_username = explode("@", $email);
                    $firstname = $extract_username[0];
                } else {
                    $name = $providerUser->getName();
                    $parts = explode(" ", $name);
                    if (count($parts) > 1) {
                        $lastname = array_pop($parts);
                        $firstname = implode(" ", $parts);
                    } else {
                        $firstname = $name;
                    }
                }
                $pelapor = new Pelapor;
                $pelapor->firstname = $firstname;
                $pelapor->lastname = $lastname;
                $pelapor->email = $providerUser->getEmail();
                $pelapor->avatar =  $providerUser->getAvatar();
                $pelapor->provider = $provider;
                $pelapor->save();

                Auth::guard('pelapors-api')->login($pelapor);
            }
            $pelapor->LinkedSocialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);

            Auth::guard('pelapors-api')->login($pelapor);
            return $pelapor;
        }
    }
}
