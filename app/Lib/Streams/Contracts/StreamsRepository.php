<?php

namespace App\Lib\Streams\Contracts;

use App\Lib\Streams\Models\Stream;
use App\Lib\Streams\Models\StreamContainer;

/**
 * Interface StreamsRepository
 *
 * @package App\Lib\Streams\Contracts
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
interface StreamsRepository
{
    /**
     * @param string $streamId
     * @return array
     */
    public function fetch(string $streamId) : array;

    /**
     * @return StreamContainer
     */
    public function all() : StreamContainer;
}
