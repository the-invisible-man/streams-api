<?php

namespace App\Lib\Streams;

use App\Lib\Streams\StreamsService;
use Illuminate\Support\ServiceProvider;
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
            //
        });

        $this->app->singleton(StreamsService::class, function (Application $app, array $params = [])
        {
            //
        });
    }
}
