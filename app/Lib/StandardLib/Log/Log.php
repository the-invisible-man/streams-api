<?php

namespace App\Lib\StandardLib\Log;

use Illuminate\Log\Writer;
use Monolog\Logger as MonologLogger;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class Log
 *
 * @package App\Lib\StandardLib\Log
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class Log extends Writer
{
    // Log constants
    const   DEBUG       = 'debug',
            INFO        = 'info',
            NOTICE      = 'notice',
            WARNING     = 'warning',
            ERROR       = 'error',
            CRITICAL    = 'critical',
            ALERT       = 'alert',
            EMERGENCY   = 'emergency';

    /**
     * @var null|string This uid helps us find logs for a request.
     */
    protected $uid;

    /**
     * Log constructor.
     *
     * @param MonologLogger $monolog
     * @param Dispatcher $dispatcher
     * @param string|null $uid
     */
    public function __construct(MonologLogger $monolog, Dispatcher $dispatcher, string $uid = null)
    {
        parent::__construct($monolog, $dispatcher);

        $this->uid = $uid;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return $this
     */
    public function log($level, $message, array $context = [])
    {
        parent::log($level, $message, $this->prepareContext($context));

        return $this;
    }

    /**
     * @param array $context
     * @return array
     */
    public function prepareContext(array $context)
    {
        $context['_request']             = [];
        $context['_request']['uid']      = $this->uid;

        return $context;
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function debug($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function info($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function notice($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function warning($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function error($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function critical($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function alert($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     * @return Log
     */
    public function emergency($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }
}
