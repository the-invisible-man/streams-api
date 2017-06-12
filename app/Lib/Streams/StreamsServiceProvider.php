<?php

namespace App\Lib\Streams;

use MongoDB\Client;
use App\Lib\Ads\AdsService;
use App\Lib\StandardLib\Log\Log;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use App\Lib\StandardLib\Services\CacheService;
use App\Lib\Streams\Repositories\MongoStreams;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class StreamsServiceProvider
 *
 * @package App\Lib\Streams
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class StreamsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MongoStreams::class, function (Application $app, array $params = [])
        {
            $mongo  = $app->make(Client::class);
            $config = $app['config']['streams.repositories.' . MongoStreams::class];

            return new MongoStreams($config, $mongo);
        });

        $this->app->singleton(StreamsService::class, function (Container $app, array $params = [])
        {
            $config = $app['config']['services.' . StreamsService::class];
            $repo   = $app->make(MongoStreams::class);
            $cache  = $app->makeWith(CacheService::class, ['service-identifier' => 'streams']);
            $log    = $app->make(Log::class);
            $ads    = $app->make(AdsService::class);

            return new StreamsService($config, $repo, $cache, $log, $ads);
        });
    }
}
