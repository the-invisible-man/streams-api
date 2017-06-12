<?php

namespace App\Lib\StandardLib;

use App\Lib\StandardLib\Log\Log;
use Illuminate\Support\ServiceProvider;
use App\Lib\StandardLib\Tools\CacheWarmer;
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

    /**
     * @var CacheService[]
     */
    private static $caches = [];

    public function register()
    {
        $this->app->bind(CacheService::class, function (Application $app, array $params = [])
        {
            $this->hasKeys(['service-identifier'], $params);

            if (!isset(self::$caches[$params['service-identifier']]))
            {
                $cache = $app->make(CacheRepository::class);
                $log   = $app->make(Log::class);

                self::$caches[$params['service-identifier']] = new CacheService($params['service-identifier'], $cache, $log);
            }

            return self::$caches[$params['service-identifier']];
        });

        $this->app->singleton(RequestIdentifier::class, function (Application $app, array $params = [])
        {
            return new RequestIdentifier();
        });

        $this->app->singleton(Log::class, function (Application $app, array $params = [])
        {
            $identifier = $app->make(RequestIdentifier::class);
            $monologger = $app->make(\Illuminate\Log\Writer::class)->getMonolog();
            $dispatcher = $app->make(Dispatcher::class);

            $syslog    = new \Monolog\Handler\SyslogUdpHandler("logs5.papertrailapp.com", 11586);
            $formatter = new \Monolog\Formatter\LineFormatter('%channel%.%level_name%: %message% %extra%');

            $syslog->setFormatter($formatter);
            $monologger->pushHandler($syslog);

            return new Log($monologger, $dispatcher, $identifier->get());
        });

        $this->app->singleton(ResponseBuilder::class, function (Application $app, array $params = [])
        {
            $config             = $app['config']['app.response'];
            $config['debug']    = $app['config']['app.debug'];
            $factory            = $app->make(ResponseFactory::class);
            $requestIdentifier  = $app->make(RequestIdentifier::class);

            return new ResponseBuilder($config, $factory, $requestIdentifier);
        });

        $this->app->singleton(CacheWarmer::class, function (Application $app, array $params = [])
        {
            $warmers    = $app['config']['cache.warmers'];
            $container  = [];

            foreach ($warmers as $warmer)
            {
                $container[] = $app->make($warmer);
            }

            return new CacheWarmer($container);
        });
    }
}
