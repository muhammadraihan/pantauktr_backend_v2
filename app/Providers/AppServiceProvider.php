<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // fix mysql string length error
        Schema::defaultStringLength(191);
        // force apps to use secure protocol
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        // passport routes
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
        // passport token lifetimes
        Passport::tokensExpireIn(Carbon::now()->addDays(env('PASSPORT_ACCESS_TOKEN_EXPIRES')));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(env('PASSPORT_REFRESH_TOKEN_EXPIRES')));
    }
}
