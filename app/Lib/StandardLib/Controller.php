<?php

namespace App\Lib\StandardLib;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Input;
use App\Lib\StandardLib\Exceptions\ErrorBag;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Lib\StandardLib\Support\Responses\CustomResponse;
use App\Lib\StandardLib\Exceptions\ResponseProcessException;

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
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ValidatesConfig;

    const OK    = 'OK';
    const ERROR = 'ERROR';

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var array
     */
    protected $config;

    /**
     * Controller constructor.
     * @param array $config
     * @param ValidatorFactory $factory
     */
    public function __construct(array $config, ValidatorFactory $factory)
    {
        $this->config           = $this->validateConfig($config);
        $this->validatorFactory = $factory;
    }

    /**
     * @return array
     */
    protected function getRequiredConfig() : array
    {
        return [''];
    }

    /**
     * @param mixed $data
     * @param string $status
     * @param string|string[] $messages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respond($data = [], string $status = self::OK, $messages = [], int $code = null) : JsonResponse
    {
        // You can specify the HTTP response code you wish
        // to send back, but if no code is set and the status
        // is not OK then it is automatically turned into a 400 (Bad Request).
        if ($status != self::OK && $code == null) {
            $code = 400;
        } elseif ($code == null) {
            $code = 200;
        }

        // Standard API response
        $response = [
            'status'         => $status,
            'messages'       => self::formatErrorMessages($messages),
            'data'           => $data
        ];

        return response()->json(self::buildResponse($response), $code);
    }

    /**
     * @param array $response
     * @return array
     */
    public static function extractCustomResponse(array $response) : array
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

        return $response;
    }

    /**
     * @param array $response
     * @return array
     * @throws ResponseProcessException
     */
    public static function buildResponse(array $response) : array
    {
        $response = self::extractCustomResponse($response);

        if (config('app.api.response.overload')) {

            // Even though we are calling static methods and one would think this
            // becomes untestable, laravel's IoC pattern allows us to set the configs
            // in it's container so that all static calls to 'App' can be injected with test data.
            // This isn't something we should go crazy with, but not too bad to use every now and then
            $handler = \App::make(config('app.api.response.overload_handler'));

            if (is_callable($handler)) {
                return $handler($response);
            }

            throw new ResponseProcessException("Cannot respond API because the standard response overload_handler is not a callable object.");
        }

        return $response;
    }

    /**
     * @param $messages
     * @return array
     */
    protected static function formatErrorMessages($messages) : array
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
     * Get a validator for an incoming request.
     * Second parameter allows a list of fields to validate.
     * If no rules are passed then all rules are applied.
     *
     * @param  array  $data
     * @param  array  $rules
     * @return Validator
     */
    protected function validator(array $data, array $rules = null) : Validator
    {
        if (is_null($rules)) {
            $rules = $this->validationRules;
        } else {
            // This lets us specify which validators from the $validationRules
            // array you'd like to use on the input.
            $rules = array_intersect_key($this->validationRules, array_flip($rules));
        }

        $messages = property_exists($this, 'messages') ? $this->messages : [];

        return $this->validatorFactory->make($data, $rules, $messages);
    }

    /**
     * @param Validator $validator
     * @return array
     * @see link to docs
     */
    public static function getValidatorMessages(Validator $validator) : array
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
    public static function getExceptionMessage(\Throwable $e)
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
