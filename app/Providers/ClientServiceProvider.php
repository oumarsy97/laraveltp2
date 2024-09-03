<?php

namespace App\Providers;

use App\Interfaces\ClientInterface;
use App\Services\Http\HttpClient ;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;


class ClientServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ClientRepositoryInterface::class, HttpClient::class);
    }
}
