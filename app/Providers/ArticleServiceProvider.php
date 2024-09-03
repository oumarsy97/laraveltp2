<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\IArticleService;
use App\Services\ArticleService;

class ArticleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(IArticleService::class, ArticleService::class);
    }

    public function boot()
    {
        //
    }
}
