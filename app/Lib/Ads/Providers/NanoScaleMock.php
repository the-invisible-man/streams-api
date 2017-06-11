<?php

namespace App\Lib\Ads\Providers;

use GuzzleHttp\Client;
use App\Lib\Ads\Models\AdsContainer;
use App\Lib\Ads\Contracts\AdsRepository;
use App\Lib\Ads\Exceptions\AdServiceOutage;
use Illuminate\Contracts\Support\Arrayable;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;

/**
 * Class NanoScaleMock
 *
 * @package App\Lib\Ads\Providers
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class NanoScaleMock implements AdsRepository
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
     * @return AdsContainer
     * @throws AdServiceOutage
     */
    public function fetch(string $streamId) : AdsContainer
    {
        $response = $this->http->request('GET', $streamId);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new AdServiceOutage("Ad service is currently not available.");
        }
    }
}
