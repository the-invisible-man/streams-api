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
    /**
     * Should returned number of items loaded
     * @return int
     */
    public function warmUp() : int;

    /**
     * A name to identify this warmer with.
     * @return string
     */
    public function name() : string;
}
