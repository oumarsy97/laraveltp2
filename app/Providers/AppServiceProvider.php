<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Repositories\ClientRepository;
use App\Repositories\Contracts\IClientRepository;
use App\Repositories\Contracts\IDetteRepository;
use App\Repositories\Contracts\IUserRepository;
use App\Repositories\DetteRepository;
use App\Repositories\UserRepository;
use App\Services\ClientService;
use App\Services\CloudinaryUploadService;
use App\Services\Contracts\IClientService;
use App\Services\Contracts\IDetteService;
use App\Services\Contracts\IFileManager;
use App\Services\Contracts\ILocalStorageService;
use App\Services\Contracts\IUploadService;
use App\Services\Contracts\IUserService;
use App\Services\Contracts\LoyaltyCardServiceInterface;
use App\Services\DetteService;
use App\Services\FileManager;
use App\Services\LocalStorageService;
use App\Services\LoyaltyCardService;
use App\Services\UploadService;
use App\Services\UserService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistrement des Repository
        $this->app->singleton(IUserRepository::class, UserRepository::class);
        $this->app->alias(IUserRepository::class, 'userRepository');

        $this->app->singleton(IClientRepository::class, ClientRepository::class);
        $this->app->alias(IClientRepository::class, 'clientRepository');

        $this->app->singleton(IDetteRepository::class, DetteRepository::class);
        $this->app->alias(IDetteRepository::class, 'detteRepository');


        // Enregistrement des Services
        $this->app->singleton(IUserService::class, UserService::class);
        $this->app->alias(IUserService::class, 'userService');
        $this->app->singleton(IClientService::class, ClientService::class);
        $this->app->alias(IClientService::class, 'clientService');
        $this->app->singleton(IDetteService::class, DetteService::class);
        $this->app->alias(IDetteService::class, 'detteService');


        $this->app->singleton(IUploadService::class, CloudinaryUploadService::class);
        $this->app->alias(IUploadService::class, 'cloudinaryUploadService');
        $this->app->singleton('loyaltyCard', function ($app) {
            return new LoyaltyCardService();
        });
        // $this->app->singleton('uploadservice', function ($app) {
        //     return new CloudinaryUploadService();
        // });
        // $this->app->singleton(IUploadService::class, CloudinaryUploadService::class);
        // $this->app->alias(IUploadService::class, 'cloudinaryUploadService');

           // Lier l'interface à l'implémentation
        $this->app->bind(LoyaltyCardServiceInterface::class, LoyaltyCardService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    //   User::observe(UserObserver::class);
    }
}
