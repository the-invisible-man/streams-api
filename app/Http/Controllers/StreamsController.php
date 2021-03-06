<?php

namespace App\Http\Controllers;

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
     * @var StreamsService
     */
    private $streamsService;

    /**
     * StreamsController constructor.
     *
     * @param ResponseBuilder $responseBuilder
     * @param StreamsService $streamsService
     */
    public function __construct(
        ResponseBuilder     $responseBuilder,
        StreamsService      $streamsService
    ) {
        parent::__construct($responseBuilder);

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
        return $this->respond(
            $this->streamsService->fetch($streamId)
        );
    }
}
