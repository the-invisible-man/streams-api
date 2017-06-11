<?php

namespace App\Lib\StandardLib\Support\Responses;

/**
 * Interface CustomResponse
 *
 * @package App\Lib\StandardLib\Support\Responses
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
interface CustomResponse
{
    /**
     * @return array
     */
    public function getData() : array;

    /**
     * @return array
     */
    public function getTopLevel() : array;
}