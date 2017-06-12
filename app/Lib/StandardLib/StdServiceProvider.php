<?php

namespace App\Lib\StandardLib;

use App\Lib\StandardLib\Log\Log;
use Monolog\Logger as MonologLogger;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use App\Lib\StandardLib\Services\CacheService;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Cache\Repository as CacheRepository;
use App\Lib\StandardLib\Services\Http\ResponseBuilder;
use App\Lib\StandardLib\Services\Http\RequestIdentifier;

/**
 * Class StdServiceProvider
 *
 * @package App\Lib\StandardLib
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class StdServiceProvider extends ServiceProvider
{
    use ChecksArrayKeys;

    public function register()
    {
        $this->app->bind(CacheService::class, function (Application $app, array $params = [])
        {
            $this->hasKeys(['service-identifier'], $params);

            $cache = $app->make(CacheRepository::class);

            return new CacheService($params['service-identifier'], $cache);
        });

        $this->app->singleton(RequestIdentifier::class, function (Application $app, array $params = [])
        {
            return new RequestIdentifier();
        });

        $this->app->singleton(Log::class, function (Application $app, array $params = [])
        {
            $identifier = $app->make(RequestIdentifier::class);
            $monologger = $app->make(MonologLogger::class);
            $dispatcher = $app->make(Dispatcher::class);

            return new Log($monologger, $dispatcher, $identifier->get());
        });

        $this->app->singleton(ResponseBuilder::class, function (Application $app, array $params)
        {
            $config             = $app['config']['app.response'];
            $config['debug']    = $app['config']['app.debug'];
            $factory            = $app->make(ResponseFactory::class);
            $requestIdentifier  = $app->make(RequestIdentifier::class);

            return new ResponseBuilder($config, $factory, $requestIdentifier);
        });
    }
}
