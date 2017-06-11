<?php

namespace App\Lib\StandardLib\Services\Http;

/**
 * Class RequestIdentifier
 *
 * @package App\Lib\StandardLib\Services\Http
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class RequestIdentifier
{
    /**
     * @var string
     */
    private $uid;

    /**
     * RequestIdentifier constructor.
     */
    public function __construct()
    {
        $this->uid = uniqid();
    }

    /**
     * @return string
     */
    public function get() : string
    {
        return $this->uid;
    }
}
