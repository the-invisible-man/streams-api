<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Lib\Streams\StreamsService;
use App\Lib\StandardLib\Controller;
use App\Lib\Ads\Contracts\AdProvider;
use Illuminate\Validation\Factory as ValidatorFactory;

/**
 * Class StreamsController
 * @package App\Http\Controllers
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class StreamsController extends Controller
{
    /**
     * @var AdProvider
     */
    private $adService;

    /**
     * @var StreamsService
     */
    private $streamsService;

    /**
     * StreamsController constructor.
     *
     * @param array $config
     * @param ValidatorFactory $factory
     * @param AdProvider $adProvider
     * @param StreamsService $streamsService
     */
    public function __construct(
        array               $config,
        ValidatorFactory    $factory,
        AdProvider          $adProvider,
        StreamsService      $streamsService
    ) {
        parent::__construct($config, $factory);

        $this->adService        = $adProvider;
        $this->streamsService   = $streamsService;
    }

    /**
     * @return array
     */
    protected function getRequiredConfig() : array
    {
        return array_merge([], parent::getRequiredConfig());
    }

    /**
     * @return JsonResponse
     */
    public function all() : JsonResponse
    {

    }

    /**
     * @param int $streamId
     * @return JsonResponse
     */
    public function get(int $streamId) : JsonResponse
    {

    }
}
