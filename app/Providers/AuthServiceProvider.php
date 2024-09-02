<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    // public function boot(): void
    // {
    //     //
    //     // Optionnel : Définir la durée de vie des tokens
    //     Passport::tokensExpireIn(now()->addDays(15));
    //     Passport::refreshTokensExpireIn(now()->addDays(30));
    // }
   /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        // Ensure the Passport routes are registered
        Passport::ignoreRoutes();
        $this->registerPolicies();

    Gate::define('admin', [UserPolicy::class, 'isAdmin']);
    Gate::define('boutiquier', [UserPolicy::class, 'isBoutiquier']);
    Gate::define('client', [UserPolicy::class, 'isClient']);

    // Définir la durée de validité des tokens
    Passport::tokensExpireIn(now()->addDays(15));
    Passport::refreshTokensExpireIn(now()->addDays(30));
    Passport::personalAccessTokensExpireIn(now()->addMonths(6));
}

}
