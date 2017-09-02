<?php

namespace App\Exceptions;

use Exception;
use App\Lib\StandardLib\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use App\Lib\StandardLib\Services\Http\ResponseBuilder;
use App\Lib\Streams\Exceptions\StreamNotFoundException;
use App\Lib\StandardLib\Exceptions\RecordNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    // We can respond for specific exceptions. Not meant for specific exceptions but more general.
    protected $exceptionResponses = [
        RecordNotFoundException::class      => ['message' => 'Resource not found', 'code' => 404],
        StreamNotFoundException::class      => ['message' => 'Resource not found', 'code' => 404]
    ];

    /**
     * @var ResponseBuilder
     */
    protected $responseBuilder;

    /**
     * Handler constructor.
     * @param Container $container
     * @param ResponseBuilder $responseBuilder
     */
    public function __construct(Container $container, ResponseBuilder $responseBuilder)
    {
        parent::__construct($container);

        $this->responseBuilder = $responseBuilder;
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $this->report($e);
        $ec = get_class($e);

        if (array_key_exists($ec, $this->exceptionResponses)){
            $data = $this->exceptionResponses[$ec];
            $code = isset($data['code']) ? $data['code'] : null;

            if (!$code) {
                if (method_exists($e, 'getStatusCode')) {
                    $code = $e->getStatusCode();
                } else {
                    $code = 400;
                }
            }

            return $this->responseBuilder->respond('', Controller::ERROR, $data['message'], $code);
        }

        // We only want to send the actual exception message if debug mode is enabled
        $message    = $this->responseBuilder->getExceptionMessage($e);
        $code       = 500;

        // Send standard error message
        return $this->responseBuilder->respond('', Controller::ERROR, $message, $code);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
