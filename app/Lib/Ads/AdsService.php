<?php

namespace App\Lib\Ads;

use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Ads\Contracts\AdsRepository;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use App\Lib\StandardLib\Services\CacheService as AdsCacheService;

/**
 * Class AdsService
 *
 * @package App\Lib\Ads
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class AdsService
{
    use Logs, ValidatesConfig;

    /**
     * @var AdsRepository
     */
    private $repository;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var array
     */
    private $config;

    /**
     * @var AdsCacheService
     */
    private $cache;

    /**
     * AdsService constructor.
     * @param array $config
     * @param AdsRepository $repository
     * @param Log $log
     * @param AdsCacheService $cache
     */
    public function __construct(array $config, AdsRepository $repository, Log $log, AdsCacheService $cache)
    {
        $this->config       = $this->validateConfig($config);
        $this->repository   = $repository;
        $this->log          = $log;
        $this->cache        = $cache;
        $this->logNamespace = 'AdsService';
    }

    /**
     * @return array
     */
    public function getRequiredConfig() : array
    {
        return ['bail_if_down', 'cache'];
    }

    /**
     * @return bool
     */
    public function cacheEnabled() : bool
    {
        return (bool)$this->config['cache'];
    }

    /**
     * @return Log
     */
    public function getLog() : Log
    {
        return $this->log;
    }

    /**
     * @param string $streamId
     * @return array
     * @throws \Throwable
     */
    public function fetch(string $streamId) : array
    {
        $ads = [];

        try {
            if ($this->config['cache'] && $this->cache->has($streamId))
            {
                $this->log(Log::INFO, "Fetching advertisement data for stream with id {$streamId} from cache.");
                $ads = $this->cache->get($streamId);
            }
            else {
                $this->log(Log::INFO, "Fetching fresh stream object with id {$streamId} from repository.");
                // Fetch fresh copy from repository
                $ads = $this->repository->fetch($streamId);

                if ($this->config['cache']) {
                    $this->log(Log::INFO, "Caching is enabled, adding object to cache.");
                    $this->cache->put($streamId, $ads);
                } else {
                    $this->log(Log::INFO, "Caching is disabled");
                }
            }

        } catch (\Throwable $e) {
            if ($this->config['bail_if_down']) {
                throw $e;
            }
            // We can configure the app to not interrupt the streams if the ads fail.
            $this->log(Log::CRITICAL, "Unable to fetch advertisements for stream id: {$streamId}");
        }

        return $ads;
    }
}
