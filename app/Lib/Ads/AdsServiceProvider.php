<?php

namespace App\Lib\Ads;

use GuzzleHttp\Client;
use App\Lib\StandardLib\Log\Log;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use App\Lib\Ads\Providers\NanoScaleMock;
use App\Lib\Ads\Contracts\AdsRepository;
use App\Lib\StandardLib\Services\CacheService;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class AdsServiceProvider
 *
 * @package App\Lib\Ads
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class AdsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NanoScaleMock::class, function (Application $app, array $params = [])
        {
            $configPath = 'ads.providers.' . NanoScaleMock::class . '.api-url';
            $apiUrl     = $app['config'][$configPath];
            $client     = new Client(['base_uri' => $apiUrl]);
            $log        = $app->make(Log::class);

            return new NanoScaleMock($client, $log);
        });

        $this->app->singleton(AdsRepository::class, function (Application $app, array $params = [])
        {
            // Get default
            $default = $app['config']['ads.default'];

            return $app->make($default);
        });

        $this->app->singleton(AdsService::class, function (Container $app, array $params = [])
        {
            $conf       = $app['config']['services.' . AdsService::class];
            $adsRepo    = $app->make(AdsRepository::class);
            $log        = $app->make(Log::class);
            $cache      = $app->makeWith(CacheService::class, ['service-identifier' => 'AdsService']);

            return new AdsService($conf, $adsRepo, $log, $cache);
        });
    }
}
