<?php

namespace App\Providers;

use App\Repositories\ClientRepository;
use App\Repositories\Contracts\IClientRepository;
use App\Services\ClientService;
use App\Services\Contracts\IClientService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistrement des Repository
        $this->app->singleton(IClientRepository::class, ClientRepository::class);
        $this->app->alias(IClientRepository::class, 'clientRepository');

        // Enregistrement des Services
        $this->app->singleton(IClientService::class, ClientService::class);
        $this->app->alias(IClientService::class, 'clientService');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
