<?php

namespace App\Lib\StandardLib\Services;

use App\Lib\StandardLib\Exceptions\BadInputException;
use Illuminate\Contracts\Cache\Repository as CacheContract;

/**
 * Class CacheService
 *
 * A decorator so that we can control the cache keys. A new instance of this object
 * should be created for each service. All instances will still use the same redis connection.
 *
 * Maybe a little overkill, but this class removes the logic for creating a cache
 * key from the application so that all our services can use the same convention for their cache keys.
 *
 * @package App\Lib\StandardLib\Services
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class CacheService
{
    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var CacheContract
     */
    private $cache;

    /**
     * StreamsService constructor.
     *
     * @param string $serviceIdentifier
     * @param CacheContract $cacheRepository
     */
    public function __construct(string $serviceIdentifier, CacheContract $cacheRepository)
    {
        $this->serviceId    = $serviceIdentifier;
        $this->cache        = $cacheRepository;
    }

    /**
     * @param  string $objectId
     * @return string
     * @throws BadInputException
     */
    protected function makeKey(string $objectId) : string
    {
        if (!strlen($objectId)) {
            throw new BadInputException("Unable to build cache key, object id is a blank string.");
        }

        // Basically looks like: streams[1]
        return $this->serviceId . '[' . $objectId . ']';
    }

    /**
     * @param  string $objectId
     * @return bool
     */
    public function has(string $objectId) : bool
    {
        return $this->cache->has($this->makeKey($objectId));
    }

    /**
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->cache->get($this->makeKey($key), $default);
    }

    /**
     * @param  string $key
     * @return bool
     */
    public function forget($key) : bool
    {
        return $this->cache->forget($this->makeKey($key));
    }
}