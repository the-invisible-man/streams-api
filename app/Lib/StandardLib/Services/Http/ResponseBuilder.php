<?php

namespace App\Lib\StandardLib\Services\Http;

use Illuminate\Http\JsonResponse;
use App\Lib\StandardLib\Controller;
use Illuminate\Validation\Validator;
use App\Lib\StandardLib\Exceptions\ErrorBag;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use Illuminate\Contracts\Routing\ResponseFactory;

/**
 * Class ResponseBuilder
 *
 * @package App\Lib\StandardLib\Services\Http
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class ResponseBuilder
{
    use ValidatesConfig;

    /**
     * @var array
     */
    private $config;

    /**
     * @var ResponseFactory
     */
    private $factory;

    /**
     * @var RequestIdentifier
     */
    private $identifier;

    /**
     * ResponseBuilder constructor.
     * @param array $config
     * @param ResponseFactory $factory
     * @param RequestIdentifier $identifier
     */
    public function __construct(array $config, ResponseFactory $factory, RequestIdentifier $identifier)
    {
        $this->config       = $this->validateConfig($config);
        $this->factory      = $factory;
        $this->identifier   = $identifier;
    }

    /**
     * @return array
     */
    protected function getRequiredConfig() : array
    {
        return ['respond_uuid'];
    }

    /**
     * @param mixed $data
     * @param string $status
     * @param string|string[] $messages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data = [], string $status = Controller::OK, $messages = [], int $code = null) : JsonResponse
    {
        // You can specify the HTTP response code you wish
        // to send back, but if no code is set and the status
        // is not OK then it is automatically turned into a 400 (Bad Request).
        if ($status != Controller::OK && $code == null) {
            $code = 400;
        } elseif ($code == null) {
            $code = 200;
        }

        // Standard API response
        $response = [
            'status'         => $status,
            'messages'       => $this->formatErrorMessages($messages),
            'data'           => $data
        ];

        return $this->factory->json($this->buildResponse($response), $code);
    }

    /**
     * @param array $response
     * @return array
     */
    public function buildResponse(array $response) : array
    {
        if (!(is_object($response['data']) && in_array(CustomResponse::class, class_implements($response['data']))))
        {
            return $response;
        }

        /**
         * @var CustomResponse $customResponse
         */
        $customResponse = $response['data'];
        $response       = array_merge($response, $customResponse->getTopLevel());

        $response['data'] = $customResponse->getData();

        if ($this->config['respond_uuid']) {
            $response['uuid'] = $this->identifier->get();
        }

        return $response;
    }

    /**
     * @param $messages
     * @return array
     */
    protected function formatErrorMessages($messages) : array
    {
        // The format we use for error messages is an array of strings.
        // We will always send error messages back as a list of strings and an object.
        if (is_scalar($messages)) {
            // If this a string on non zero length then send back inside array list.
            if (strlen(preg_replace("/[^a-zA-Z0-9]/", '', $messages))) {
                return [$messages];
            }

            // String was empty, send empty array
            return [];
        }

        if (is_array($messages)) {
            if (!count($messages)) {
                return $messages;
            }

            // Check if this is an associative array, we'll guess by checking
            // that the keys are not numerical and in sequential order.
            if (array_keys($messages) !== range(0, count($messages) - 1)) {
                return array_values($messages);
            }
        }

        return $messages;
    }

    /**
     * @param Validator $validator
     * @return array
     * @see link to docs
     */
    public function getValidatorMessages(Validator $validator) : array
    {
        $failures   =  $validator->errors()->getMessages();
        $out        = [];

        // Laravel sends back its errors as an associative array, the key being the
        // field that failed and the value an array of error message strings.
        //
        // We use this function to format the Laravel validator messages
        // into a list of strings, as per the API response specifications. The new
        // format looks like this:
        //
        // String[] ->  ["Error on field {field_name}: {error_message}"]

        foreach ($failures as $field => $messages) {
            $out = array_merge($out, array_map(function ($message) use ($field) {
                return "Error on field '{$field}': {$message}";
            }, $messages));
        }

        return $out;
    }

    /**
     * @param \Throwable $e
     * @return string
     */
    public function getExceptionMessage(\Throwable $e)
    {
        // Exceptions that use the ClientReadable trait will have their
        // exception messages displayed in the API error response.
        $traits = class_uses($e);

        if (in_array(ClientReadable::class, $traits)) {
            $messages =  ($e instanceof ErrorBag) ? $e->all() : [$e->getMessage()];
        } else {
            if (\Config::get('app.debug')) {
                $messages = $e->getMessage();
            } else {
                // Confound my luck! I was not really able to avoid these facades. Controllers
                // will need to have their own constructor most of the times, it'd be annoying
                // having to call parent::__constructor() every time.
                $messages = \Config::get('core.error-message');
            }
        }

        return $messages;
    }
}
