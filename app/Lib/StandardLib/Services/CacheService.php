<?php

namespace App\Lib\StandardLib\Services;

use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use Illuminate\Contracts\Cache\Store;
use App\Lib\StandardLib\Exceptions\BadInputException;
use App\Lib\StandardLib\Exceptions\CacheServiceException;

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
    use Logs;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var Store
     */
    private $cache;

    /**
     * @var Log
     */
    private $log;

    /**
     * StreamsService constructor.
     *
     * @param string $serviceIdentifier
     * @param Store $cacheRepository
     * @param Log $log
     */
    public function __construct(string $serviceIdentifier, Store $cacheRepository, Log $log)
    {
        $this->serviceId    = $serviceIdentifier;
        $this->cache        = $cacheRepository;
        $this->log          = $log;
        $this->logNamespace = 'CacheService:' . $this->serviceId;
    }

    /**
     * @return Log
     */
    protected function getLog() : Log
    {
        return $this->log;
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
        $key = $this->serviceId . '[' . $objectId . ']';

        $this->log(Log::INFO, "Building cache key: {$key}");

        return $key;
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

    /**
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes = null)
    {
        $this->cache->put($this->makeKey($key), $value, $minutes);
    }

    /**
     * @param array $keys
     * @return array
     * @throws BadInputException
     */
    public function many(array $keys) : array
    {
        $fKeys = [];

        foreach ($keys as $key)
        {
            $fKeys[] = $this->makeKey($key);
        }

        return $this->cache->many($fKeys);
    }

    /**
     * @param array $tags
     * @param string $key
     * @param $value
     * @param null $minutes
     * @throws CacheServiceException
     */
    public function tags(array $tags, string $key, $value, $minutes = null)
    {
        if (method_exists($this->cache, 'tags')) {
            $this->cache->tags($tags)->put($key, $value, $minutes);
        }

        throw new CacheServiceException("Cannot use tags because current cache driver does not support tagging.");
    }
}
