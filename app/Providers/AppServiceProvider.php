<?php

namespace App\Providers;

use App\Lib\Ads\AdsService;
use App\Support\AdsCacheWarmer;
use App\Lib\StandardLib\Log\Log;
use MongoDB\Client as MongoClient;
use App\Lib\Streams\StreamsService;
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

        $this->app->bind(AdsCacheWarmer::class, function (Application $app, array $params = [])
        {
            $ads    = $app->make(AdsService::class);
            $stream = $app->make(StreamsService::class);
            $log    = $app->make(Log::class);

            return new AdsCacheWarmer($ads, $stream, $log);
        });
    }
}
