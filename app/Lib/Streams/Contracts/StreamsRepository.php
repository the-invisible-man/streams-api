<?php

namespace App\Lib\Streams\Contracts;

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
     * @return array
     */
    public function all();

    /**
     * @return array
     */
    public function fetchAllIds() : array;
}
