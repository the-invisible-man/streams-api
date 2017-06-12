<?php

namespace App\Http\Controllers;

use App\Lib\Ads\AdsService;
use Illuminate\Http\JsonResponse;
use App\Lib\Streams\StreamsService;
use App\Lib\StandardLib\Controller;
use App\Lib\StandardLib\Services\Http\ResponseBuilder;

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
     * @param ResponseBuilder $responseBuilder
     * @param AdsService $adsService
     * @param StreamsService $streamsService
     */
    public function __construct(
        ResponseBuilder     $responseBuilder,
        AdsService          $adsService,
        StreamsService      $streamsService
    ) {
        parent::__construct($responseBuilder);

        $this->adsService       = $adsService;
        $this->streamsService   = $streamsService;
    }

    /**
     * @return JsonResponse
     */
    public function all() : JsonResponse
    {
        return $this->respond(
            $this->streamsService->all()
        );
    }

    /**
     * @param string $streamId
     * @return JsonResponse
     */
    public function get(string $streamId) : JsonResponse
    {
        $stream = $this->streamsService->fetch($streamId);
        $stream = $stream->toArray();
        $ads    = $this->adsService->fetch($streamId);

        // Append ad data to stream
        $stream['ads'] = $ads;

        return $this->respond($stream);
    }
}
