<?php

namespace App\Lib\StandardLib\Traits;

/**
 * Class IteratesIntoArray
 *
 * @package App\Lib\StandardLib\Traits
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
trait IteratesIntoArray
{
    /**
     * This can be a costly operation for large complex structures, that's why caching is important
     * @param \ArrayObject $object
     * @param bool         $recursive
     * @return array
     */
    protected function iteratorToArray(\ArrayObject $object, bool $recursive = true) : array
    {
        $converted  = iterator_to_array($object);
        $output     = [];

        if ($recursive) {
            foreach ($converted as $key => $structure)
            {
                if ($structure instanceof \ArrayObject) {
                    $output[$key] = $this->iteratorToArray($structure, $recursive);
                } else {
                    $output[$key] = $structure;
                }
            }
        }

        return $output;
    }
}