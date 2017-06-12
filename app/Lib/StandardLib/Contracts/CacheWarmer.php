<?php

namespace App\Lib\StandardLib\Contracts;

/**
 * Interface CacheWarmerInterface
 *
 * @package App\Lib\StandardLib\Contracts
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
interface CacheWarmer
{
    public function warmUp();
}
