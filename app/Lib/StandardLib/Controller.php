<?php

namespace App\Lib\StandardLib;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use App\Lib\StandardLib\Services\Http\ResponseBuilder;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Lib\StandardLib\Services\Http\RequestIdentifier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 *
 * This is the base controller for the entire app. It allows
 * us to leverage the features of laravel to better integrate
 * with our app.
 *
 * @package App\Lib\StandardLib
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const OK    = 'OK';
    const ERROR = 'ERROR';

    /**
     * @var RequestIdentifier
     */
    protected $identifier;

    /**
     * @var ResponseBuilder
     */
    protected $responseBuilder;

    /**
     * Controller constructor.
     * @param ResponseBuilder $builder
     */
    public function __construct(ResponseBuilder $builder)
    {
        $this->responseBuilder  = $builder;
    }

    /**
     * @param array $data
     * @param string $status
     * @param array $messages
     * @param int $code
     * @return JsonResponse
     */
    protected function respond($data = [], string $status = Controller::OK, $messages = [], int $code = null) : JsonResponse
    {
        return $this->responseBuilder->respond($data, $status, $messages, $code);
    }

    /**
     * @param int|null $int
     * @return int
     */
    public function perPage(int $int = null) : int
    {
        $input = Input::get('per_page');
        return (int)( is_null($int) ? (is_null($input) ? config('app.pagination') : $input) : $int );
    }
}
