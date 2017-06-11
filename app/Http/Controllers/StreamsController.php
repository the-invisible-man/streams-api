<?php

namespace App\Http\Controllers;

use App\Lib\Ads\AdsService;
use Illuminate\Http\JsonResponse;
use App\Lib\Streams\StreamsService;
use App\Lib\StandardLib\Controller;
use App\Lib\StandardLib\Services\Http\ResponseBuilder;
use Illuminate\Validation\Factory as ValidatorFactory;

/**
 * Class StreamsController
 *
 * @package App\Http\Controllers
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class StreamsController extends Controller
{
    /**
     * @var AdsService
     */
    private $adsService;

    /**
     * @var StreamsService
     */
    private $streamsService;

    /**
     * StreamsController constructor.
     *
     * @param ValidatorFactory $factory
     * @param ResponseBuilder $responseBuilder
     * @param AdsService $adsService
     * @param StreamsService $streamsService
     */
    public function __construct(
        ValidatorFactory    $factory,
        ResponseBuilder     $responseBuilder,
        AdsService          $adsService,
        StreamsService      $streamsService
    ) {
        parent::__construct($factory, $responseBuilder);

        $this->adsService       = $adsService;
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
