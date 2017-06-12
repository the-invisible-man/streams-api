<?php

namespace App\Lib\Ads\Providers;

use GuzzleHttp\Client;
use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Ads\Contracts\AdsRepository;
use App\Lib\Ads\Exceptions\AdServiceOutage;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;

/**
 * Class NanoScaleMock
 *
 * @package App\Lib\Ads\Providers
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class NanoScaleMock implements AdsRepository
{
    use ChecksArrayKeys, Logs;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var Log
     */
    private $log;

    /**
     * NanoScaleMock constructor.
     * @param Client $client
     * @param Log $log
     */
    public function __construct(Client $client, Log $log)
    {
        $this->http     = $client;
        $this->log      = $log;

        $this->logNamespace = 'NanoScaleMock';
    }

    /**
     * @return Log
     */
    protected function getLog() : Log
    {
        return $this->log;
    }

    /**
     * @param string $streamId
     * @return array
     * @throws AdServiceOutage
     */
    public function fetch(string $streamId) : array
    {
        $response = $this->http->request('GET', $streamId);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {

            $this->log(Log::CRITICAL, "Request to NanoScale ad provider failed.", [
                'body'  => $response->getBody()->getContents(),
                'code'  => $response->getStatusCode()
            ]);

            throw new AdServiceOutage("Ad service is currently not available.");
        }

        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);

        if (!$body) {
            throw new AdServiceOutage("Unable to decode json data from NanoScale ad service even though their API returned a success http code.");
        }

        return $body;
    }
}
