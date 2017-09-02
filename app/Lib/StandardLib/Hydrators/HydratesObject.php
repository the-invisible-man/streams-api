<?php

namespace App\Lib\StandardLib\Hydrators;

/**
 * Class HydratesObject
 *
 * @package App\Lib\StandardLib\Hydrators
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
trait HydratesObject
{
    /**
     * @param array $data
     */
    public function hydrateProperties(array $data)
    {
        foreach ($data as $key => $value)
        {
            $key = $this->snakeToCamel($key);

            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * Convert snake case string to camel case
     *
     * @param $val
     * @return mixed
     */
    private function snakeToCamel($val)
    {
        return count(explode('_', $val)) === 1 ? strtolower($val) : lcfirst(str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $val)))));
    }
}