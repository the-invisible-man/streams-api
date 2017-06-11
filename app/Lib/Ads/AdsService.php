<?php

namespace App\Lib\Ads;

use App\Lib\StandardLib\Log\Log;
use App\Lib\StandardLib\Log\Logs;
use App\Lib\Ads\Models\AdsContainer;
use App\Lib\Ads\Contracts\AdsRepository;
use App\Lib\StandardLib\Traits\ValidatesConfig;

/**
 * Class AdsService
 *
 * @package App\Lib\Ads
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class AdsService
{
    use Logs, ValidatesConfig;

    /**
     * @var AdsRepository
     */
    private $repository;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var array
     */
    private $config;

    /**
     * AdsService constructor.
     * @param array $config
     * @param AdsRepository $repository
     * @param Log $log
     */
    public function __construct(array $config, AdsRepository $repository, Log $log)
    {
        $this->config       = $this->validateConfig($config);
        $this->repository   = $repository;
        $this->log          = $log;
        $this->logNamespace = 'AdsService';
    }

    /**
     * @return array
     */
    public function getRequiredConfig() : array
    {
        return ['bail_if_down'];
    }

    /**
     * @return Log
     */
    public function getLog() : Log
    {
        return $this->log;
    }

    /**
     * @param string $streamId
     * @return AdsContainer
     * @throws \Throwable
     */
    public function fetch(string $streamId) : AdsContainer
    {
        $ads = new AdsContainer();

        try {
            $ads = $this->repository->fetch($streamId);
        } catch (\Throwable $e) {
            if ($this->config['bail_if_down']) {
                throw $e;
            }

            // We can configure the app to not interrupt the streams if the ads fail.
            $this->log(Log::CRITICAL, "Unable to fetch advertisements for stream id: {$streamId}");
        }

        return $ads;
    }
}
