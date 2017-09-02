<?php

namespace App\Lib\Streams;

use App\Lib\Ads\AdsService;
use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use App\Lib\Streams\Contracts\StreamsRepository;
use App\Lib\StandardLib\Services\CacheService as StreamsCacheService;

/**
 * Class StreamsService
 *
 * @package App\Lib\Streams
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class StreamsService
{
    use Logs, ValidatesConfig, ChecksArrayKeys;

    /**
     * @var StreamsRepository
     */
    private $repository;

    /**
     * @var StreamsCacheService
     */
    private $cache;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var array
     */
    private $config;

    /**
     * @var AdsService
     */
    private $adsService;

    /**
     * StreamsService constructor.
     *
     * @param array $config
     * @param StreamsRepository $repository
     * @param StreamsCacheService $cache
     * @param Log $log
     * @param AdsService $adsService
     */
    public function __construct(
        array               $config,
        StreamsRepository   $repository,
        StreamsCacheService $cache,
        Log                 $log,
        AdsService          $adsService
    ) {
        $this->config       = $this->validateConfig($config);
        $this->repository   = $repository;
        $this->cache        = $cache;
        $this->log          = $log;
        $this->adsService   = $adsService;
        $this->logNamespace = 'StreamsService';
    }

    /**
     * @return array
     */
    public function getRequiredConfig() : array
    {
        return ['cache', 'cache_ttl'];
    }

    /**
     * @param string $streamId
     * @return array
     */
    public function fetch(string $streamId) : array
    {
        // We'll check if this service is configured to cache
        // then we're going to check if the object is already in the cache.
        if ($this->config['cache'] && !is_null($data = $this->cache->get($streamId))) {
            $this->log(Log::INFO, "Fetched stream with id {$streamId} from cache.");
        }

        // The cache was either not enabled or the object was not found in the
        // cache. Now we'll fetch the raw data objects from their respective repositories.
        else {
            $this->log(Log::INFO, "Fetching fresh stream object with id {$streamId} from repository.");

            // We'll fetch both stream and advertisement data
            $data           = $this->repository->fetch($streamId);
            $data['ads']    = $this->adsService->fetch($streamId);

            // If cache is enabled we'll persist this object to the cache.
            if ($this->config['cache']) {
                $this->log(Log::INFO, "Caching is enabled, adding object to cache.");
                $this->cache->put($streamId, $data);
            } else {
                $this->log(Log::INFO, "Caching is disabled");
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function all() : array
    {
        $container = [];
        $streamIds = [];

        // First we fetch the streams from the repository.
        foreach ($this->repository->all() as $doc)
        {
            $streamIds[]    = $doc['_id'];
            $container[]    = $doc;
        }

        // Now we're going to fetch all the ads
        $ads = $this->adsService->fetchMany($streamIds);

        foreach ($container as $stream)
        {

            $stream['ads'] = $ads[$stream['_id']];
        }

        return $container;
    }

    /**
     * @return Log
     */
    protected function getLog() : Log
    {
        return $this->log;
    }

    /**
     * @return array
     */
    public function fetchAllIds() : array
    {
        return $this->repository->fetchAllIds();
    }
}
