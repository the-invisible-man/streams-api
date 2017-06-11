<?php

namespace App\Lib\Ads\Contracts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface AdProvider
 *
 * @package App\Lib\Ads\Contracts
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
interface AdProvider
{
    /**
     * @param string $streamId
     * @return Arrayable
     */
    public function fetch(string $streamId) : Arrayable;
}
