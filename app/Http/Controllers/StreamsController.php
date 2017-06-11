<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Lib\Streams\StreamsService;
use App\Lib\StandardLib\Controller;
use App\Lib\Ads\Contracts\AdProvider;
use App\Lib\StandardLib\Services\Http\ResponseBuilder;
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
     * @param ValidatorFactory $factory
     * @param ResponseBuilder $responseBuilder
     * @param AdProvider $adProvider
     * @param StreamsService $streamsService
     */
    public function __construct(
        ValidatorFactory    $factory,
        ResponseBuilder     $responseBuilder,
        AdProvider          $adProvider,
        StreamsService      $streamsService
    ) {
        parent::__construct($factory, $responseBuilder);

        $this->adService        = $adProvider;
        $this->streamsService   = $streamsService;
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
