<?php

namespace App\Lib\Streams;

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
    /**
     * @var StreamsRepository
     */
    private $repository;

    /**
     * @var StreamsCacheService
     */
    private $cache;

    /**
     * StreamsService constructor.
     *
     * @param StreamsRepository $repository
     * @param StreamsCacheService $cache
     */
    public function __construct(StreamsRepository $repository, StreamsCacheService $cache)
    {
        $this->repository   = $repository;
        $this->cache        = $cache;
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
}
