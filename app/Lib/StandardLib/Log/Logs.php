<?php

namespace App\Lib\StandardLib\Log;

/**
 * Class Logs
 *
 * @package App\Lib\StandardLib\Log
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
trait Logs
{
    /**
     * @var string
     */
    protected $logNamespace = null;

    /**
     * @return Log
     */
    protected abstract function getLog() : Log;

    /**
     * @param $level
     * @param $message
     * @param array $context
     * @return Log
     */
    protected function log($level, $message, array $context = []) : Log
    {
        return $this->getLog()->log($level, $this->formatLogMessage($message), $context);
    }

    /**
     * @param string $message
     * @return string
     */
    private function formatLogMessage(string $message) : string
    {
        return is_null($this->logNamespace) ? $message : "[{$this->logNamespace}] {$message}";
    }
}
