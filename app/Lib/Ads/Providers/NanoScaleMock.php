<?php

namespace App\Lib\Ads\Providers;

use GuzzleHttp\Client;
use App\Lib\Ads\Contracts\AdProvider;
use Illuminate\Contracts\Support\Arrayable;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;

/**
 * Class NanoScaleMock
 *
 * @package App\Lib\Ads\Providers
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class NanoScaleMock implements AdProvider
{
    use ChecksArrayKeys;

    /**
     * @var Client
     */
    private $http;

    /**
     * NanoScaleMock constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->http     = $client;
    }

    /**
     * @param string $streamId
     * @return Arrayable
     */
    public function fetch(string $streamId) : Arrayable
    {
        $response = $this->http->request('GET', $streamId);
    }
}
