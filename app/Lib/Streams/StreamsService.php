<?php

namespace App\Lib\Streams;

use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Streams\Models\Stream;
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
    use Logs;

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
     * StreamsService constructor.
     *
     * @param StreamsRepository $repository
     * @param StreamsCacheService $cache
     * @param Log $log
     */
    public function __construct(StreamsRepository $repository, StreamsCacheService $cache, Log $log)
    {
        $this->repository   = $repository;
        $this->cache        = $cache;
        $this->log          = $log;
        $this->logNamespace = 'StreamsService';
    }

    /**
     * @param string $streamId
     * @return Stream
     */
    public function fetch(string $streamId) : Stream
    {
        if (!$this->cache->has($streamId)) {
            $data = [];
        } else {
            $data = $this->cache->get($streamId);
        }

        return $data;
    }

    /**
     * @return Log
     */
    protected function getLog() : Log
    {
        return $this->log;
    }
}
