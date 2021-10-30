<?php

namespace App\Providers;

use App\Services\SocialUserResolver;
use Carbon\Carbon;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        // binding social grant resolver interface to social resolver
        SocialUserResolverInterface::class => SocialUserResolver::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
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
