<?php

namespace App\Lib\Streams;

use App\Lib\Streams\Models\Stream;
use App\Lib\StandardLib\Services\CacheService;
use App\Lib\Streams\Contracts\StreamsRepository;

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
     * @var CacheService
     */
    private $cache;

    /**
     * StreamsService constructor.
     *
     * @param StreamsRepository $repository
     * @param CacheService $cache
     */
    public function __construct(StreamsRepository $repository, CacheService $cache)
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

        }

        // Fetch data from cache
        return $this->cache->get($streamId);
    }
}
