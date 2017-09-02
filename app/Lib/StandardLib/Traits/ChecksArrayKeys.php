<?php

namespace App\Lib\StandardLib\Traits;

use App\Lib\StandardLib\Exceptions\DataCheckException;

/**
 * Class ChecksArrayKeys
 *
 * @package App\Lib\StandardLib\Traits
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
trait ChecksArrayKeys
{
    /**
     * @param array $needles
     * @param array $haystack
     * @param string $message (Add the placeholder :message to your string and this method will inject the missing fields)
     * @return $this
     * @throws DataCheckException
     */
    public function hasKeys(array $needles, array $haystack, string $message = null)
    {
        $diff       = array_diff($needles, array_keys($haystack));
        $missing    = implode(',', $diff);
        $message    = is_null($message) ? "Array is missing the following fields: :missing" : $message;

        if (count($diff)) {
            throw new DataCheckException(str_replace(':missing', $missing, $message));
        }

        return $this;
    }
}
