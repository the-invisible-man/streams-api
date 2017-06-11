<?php

namespace App\Lib\StandardLib\Exceptions;

/**
 * Class BaseException
 *
 * @package App\Lib\StandardLib\Exceptions
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class BaseException extends \Exception
{
    /**
     * @var array
     */
    private $context;

    /**
     * @var string
     */
    private $userMessageCode = null;

    /**
     * BaseException constructor.
     *
     * @param string     $message
     * @param array      $context
     * @param null       $userMsgCode
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", array $context = [], $userMsgCode = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->context         = $context;
        $this->userMessageCode = $userMsgCode;
    }

    /**
     * @return mixed
     */
    public function getContext() : array
    {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function hasUserMessageCode() : bool
    {
        return ! ($this->userMessageCode === null);
    }
}
