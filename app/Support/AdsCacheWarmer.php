<?php

namespace App\Support;

use App\Lib\Ads\AdsService;
use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Streams\StreamsService;
use App\Lib\StandardLib\Contracts\CacheWarmer;

/**
 * Class AdsCacheWarmer
 *
 * @package App\Lib\Ads\Support
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class AdsCacheWarmer implements CacheWarmer
{
    use Logs;

    /**
     * @var AdsService
     */
    private $adsService;

    /**
     * @var StreamsService
     */
    private $streamsService;

    /**
     * @var Log
     */
    private $log;

    /**
     * AdsCacheWarmer constructor.
     *
     * @param AdsService $adsService
     * @param StreamsService $streamsService
     * @param Log $log
     */
    public function __construct(AdsService $adsService, StreamsService $streamsService, Log $log)
    {
        $this->adsService       = $adsService;
        $this->streamsService   = $streamsService;
        $this->log              = $log;
        $this->logNamespace     = 'AdsCacheWarmer';
    }

    /**
     * @return Log
     */
    protected function getLog() : Log
    {
        return $this->log;
    }

    /**
     * @return string
     */
    public function name() : string
    {
        return 'AdsCacheWarmer';
    }

    /**
     * @return int
     */
    public function warmUp() : int
    {
        if (!$this->adsService->cacheEnabled()) {
            $this->log(Log::INFO, "Cache warmer will not run because caching for ads service is not enabled.");
            return 0;
        }

        $counter = 0;

        // We can get only the ids of the streams and load the ads into the cache
        foreach ($this->streamsService->fetchAllIds() as $id)
        {
            $this->adsService->fetch($id);
            $counter++;
        }

        $this->log(Log::INFO, "Cache warmer finished, loaded {$counter} entries into the cache.");

        return $counter;
    }
}
