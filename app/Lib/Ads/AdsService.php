<?php

namespace App\Lib\Ads;

use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Ads\Contracts\AdsRepository;
use App\Lib\Ads\Exceptions\AdServiceOutage;
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
        return ['bail_if_down', 'cache', 'cache_ttl'];
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
     * @param array $streamIds
     * @return array
     * @throws AdServiceOutage
     */
    public function fetchMany(array $streamIds) : array
    {
        $inCache    = [];

        if ($this->config['cache']) {
            foreach ($this->cache->many($streamIds) as $ad) {
                if (!is_null($ad)) {
                    $inCache[$ad['stream_id']] = $ad;
                }
            }
        }

        $missing    = array_diff($streamIds, array_keys($inCache));
        $data       = array_merge($inCache, $this->repository->fetchMany($missing));

        // Validate responses and add to cache
        foreach ($missing as $streamId) {
            if (!isset($data[$streamId]) && $this->config['bail_if_down']) {
                throw new AdServiceOutage("Unable to fetch ad data for stream if {$streamId}");
            }

            $this->cache($streamId, $data[$streamId]);

            // Remove stream_id
            unset($data[$streamId]['stream_id']);
        }

        return $data;
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

            $ads = $this->getCached($streamId);

            if ($ads === null){
                $ads = $this->repository->fetch($streamId);
                $this->cache($streamId, $ads);
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

    /**
     * @param string $streamId
     * @param array $body
     */
    private function cache(string $streamId, array $body)
    {
        if ($this->config['cache']) {
            $this->log(Log::INFO, "Caching is enabled, adding object to cache.");

            $body['stream_id'] = $streamId;

            $this->cache->put($streamId, $body, $this->config['cache_ttl']);

            return;
        }

        $this->log(Log::INFO, "Caching is disabled");
    }

    /**
     * @param string $streamId
     * @return mixed|null
     */
    private function getCached(string $streamId)
    {
        if ($this->config['cache']) {
            $this->log(Log::INFO, "Fetched advertisement data for stream with id {$streamId} from cache.");
            return $this->cache->get($streamId);
        }

        $this->log(Log::INFO, "Caching is disabled");

        return null;
    }
}
