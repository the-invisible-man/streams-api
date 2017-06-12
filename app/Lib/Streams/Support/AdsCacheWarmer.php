<?php

namespace App\Lib\Streams\Support;

use App\Lib\Ads\AdsService;
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
    /**
     * AdsCacheWarmer constructor.
     * @param AdsService $adsService
     * @param StreamsService $streamsService
     */
    public function __construct(AdsService $adsService, StreamsService $streamsService)
    {

    }

    public function warmUp()
    {
        // TODO: Implement warmUp() method.
    }
}
