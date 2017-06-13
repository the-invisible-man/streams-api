<?php

namespace App\Lib\Streams;

use App\Lib\Ads\AdsService;
use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Streams\Models\Stream;
use App\Lib\Streams\Models\StreamContainer;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use App\Lib\Streams\Contracts\StreamsRepository;
use App\Lib\StandardLib\Exceptions\DataCheckException;
use App\Lib\StandardLib\Exceptions\CorruptedDataException;
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
     * @return Stream
     */
    public function fetch(string $streamId) : Stream
    {
        // We'll check if this service is configured to cache
        // then we're going to check if the object is already in the cache.
        if ($this->config['cache'] && $this->cache->has($streamId)) {
            $this->log(Log::INFO, "Fetching stream with id {$streamId} from cache.");
            $data = $this->cache->get($streamId);
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

        return $this->hydrateOne($data);
    }

    /**
     * @return StreamContainer
     */
    public function all() : StreamContainer
    {
        $container = new StreamContainer();
        $streamIds = [];

        foreach ($this->repository->all() as $doc)
        {
            $obj            = new Stream($doc);
            $streamIds[]    = $obj->getId();

            $container->attach($obj);
        }

        $ads = $this->adsService->fetchMany($streamIds);

        // Now we're going to fetch all the ads
        foreach ($container as $stream)
        {
            /**
             * @var Stream $stream
             */
            $stream->setAds($ads[$stream->getId()]);
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

    /**
     * @param array $data
     * @return Stream
     * @throws CorruptedDataException
     */
    private function hydrateOne(array $data) : Stream
    {
        try {
            // Validate the data that we received from mongo.
            $this->hasKeys(['_id', 'streamUrl', 'captions'], $data);
        } catch (DataCheckException $e) {
            // The reason that we are catching and rethrowing under a different exception is
            // because DataCheckException is thrown by the hasKeys method from the ChecksArrayKeys
            // trait and it is meant for a general use. This error however is more specific, we have
            // bad data stored, if we rethrow under this exception the main exception handler will determine
            // the appropriate Http error code, and in addition we can programmatically respond to events when
            // we have corrupted data.
            throw new CorruptedDataException($e->getMessage());
        }

        return new Stream($data);
    }
}
