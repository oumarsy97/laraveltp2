<?php

namespace App\Providers;

use App\Repositories\ClientRepository;
use App\Repositories\Contracts\IClientRepository;
use App\Services\ClientService;
use App\Services\CloudinaryUploadService;
use App\Services\Contracts\IClientService;
use App\Services\Contracts\IUploadService;
use App\Services\Contracts\LoyaltyCardServiceInterface;
use App\Services\LoyaltyCardService;
use App\Services\UploadService;
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

        $this->app->singleton('uploadservice', function ($app) {
            return new UploadService();
        });

        $this->app->singleton(IUploadService::class, CloudinaryUploadService::class);
            // Lier l'interface à l'implémentation
        $this->app->bind(LoyaltyCardServiceInterface::class, LoyaltyCardService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
