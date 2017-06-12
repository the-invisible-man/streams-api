<?php

namespace App\Lib\Ads;

use GuzzleHttp\Client;
use App\Lib\StandardLib\Log\Log;
use Illuminate\Support\ServiceProvider;
use App\Lib\Ads\Providers\NanoScaleMock;
use App\Lib\Ads\Contracts\AdsRepository;
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

            return new NanoScaleMock($client);
        });

        $this->app->singleton(AdsRepository::class, function (Application $app, array $params = [])
        {
            // Get default
            $default = $app['config']['ads.default'];

            return $app->make($default);
        });

        $this->app->singleton(AdsService::class, function (Application $app, array $params = [])
        {
            $conf       = ['bail_if_down' => false];
            $adsRepo    = $app->make(AdsRepository::class);
            $log        = $app->make(Log::class);

            return new AdsService($conf, $adsRepo, $log);
        });
    }
}
