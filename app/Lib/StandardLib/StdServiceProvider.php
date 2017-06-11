<?php

namespace App\Lib\StandardLib;

use Illuminate\Support\ServiceProvider;
use App\Lib\StandardLib\Services\CacheService;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cache\Repository as CacheRepository;

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
    }
}
