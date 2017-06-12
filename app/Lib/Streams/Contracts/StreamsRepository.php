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
     * @return Stream
     */
    public function fetch(string $streamId) : Stream;

    /**
     * @return StreamContainer
     */
    public function all() : StreamContainer;
}
