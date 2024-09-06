<?php
// app/Providers/TokenServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\TokenServiceInterface;
use App\Services\PassportTokenService;
use App\Services\SanctumTokenService;
use Illuminate\Support\Facades\Config;

class TokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $authServiceType = Config::get('auth_service.type', 'passport'); // default to 'passport'

        $this->app->bind(TokenServiceInterface::class, function ($app) use ($authServiceType) {
            switch ($authServiceType) {
                case 'sanctum':
                    return new SanctumTokenService();
                case 'passport':
                default:
                    return new PassportTokenService();
            }
        });
    }
}

