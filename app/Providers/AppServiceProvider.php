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
        Schema::defaultStringLength(191);
        
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
        
        Passport::tokensExpireIn(Carbon::now()->addDays(env('PASSPORT_ACCESS_TOKEN_EXPIRES')));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(env('PASSPORT_REFRESH_TOKEN_EXPIRES')));
    }
}
