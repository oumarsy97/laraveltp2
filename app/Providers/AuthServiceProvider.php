<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
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
        $this->registerPolicies();

        // Ensure the Passport routes are registered
        Passport::ignoreRoutes();
        $this->registerPolicies();
        Passport::tokensCan([
            'ADMIN' => 'Access all resources',
            'BOUTIQUIER' => 'Manage articles and clients',
            'CLIENT' => 'Access client-specific resources',
        ]);

    // Définir la durée de validité des tokens
    Passport::tokensExpireIn(now()->addDays(15));
    Passport::refreshTokensExpireIn(now()->addDays(30));
    Passport::personalAccessTokensExpireIn(now()->addMonths(6));
}

}
