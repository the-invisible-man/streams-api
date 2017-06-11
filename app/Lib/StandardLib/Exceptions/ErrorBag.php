<?php

namespace App\Lib\StandardLib\Exceptions;

use App\Lib\StandardLib\MessageBag;

/**
 * Class ErrorBag
 *
 * A bag of errors. This is a strange wrapper but gets around the
 * lack of multiple inheritance.
 *
 * @package App\Lib\StandardLib\Exceptions
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 * @method  array all()
 * @method  void mergeWith(string $key, array $messages)
 * @method  void add(string $key, array $message)
 */
class ErrorBag extends BaseException implements \Countable
{
    /**
     * @var MessageBag
     */
    private $errorBag;

    /**
     * ErrorBag constructor.
     * @param array $messages
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($messages = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct('', $code, $previous);
        if (is_array($messages)) {
            $this->errorBag = new MessageBag($messages);
        } elseif ($messages instanceof MessageBag) {
            $this->errorBag = $messages;
        }
    }

    /**
     * This is sort of a pseudo multiple inheritance since I couldn't extend both
     * the base Exception class and the MessageBag class.
     *
     * @param $name
     * @param $arguments
     * @return ErrorBag
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->errorBag, $name], $arguments);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->errorBag);
    }

    /**
     * @inheritdoc
     */
    public function __toString() :string
    {
        return $this->getMessage();
    }
}
