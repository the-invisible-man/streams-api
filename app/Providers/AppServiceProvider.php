<?php

namespace App\Providers;

use MongoDB\Client as MongoClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MongoClient::class, function (Application $app, array $params = [])
        {
            return new MongoClient($app['config']['database.connections.mongo.host']);
        });
    }
}
