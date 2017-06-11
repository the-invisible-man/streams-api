<?php

namespace App\Lib\Ads;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use App\Lib\Ads\Providers\NanoScaleMock;
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
            $configPath = 'ads.providers.' . NanoScaleMock::class . 'api-url';
            $apiUrl     = $app['config'][$configPath];
            $client     = new Client(['base_uri' => $apiUrl]);

            return new NanoScaleMock($client);
        });
    }
}
