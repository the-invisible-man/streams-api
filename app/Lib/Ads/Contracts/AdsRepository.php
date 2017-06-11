<?php

namespace App\Lib\Ads\Contracts;

use App\Lib\Ads\Models\AdsContainer;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface AdsRepository
 *
 * @package App\Lib\Ads\Contracts
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
interface AdsRepository
{
    /**
     * @param string $streamId
     * @return AdsContainer
     */
    public function fetch(string $streamId) : AdsContainer;
}
