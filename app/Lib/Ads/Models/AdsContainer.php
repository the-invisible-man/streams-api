<?php

namespace App\Lib\Ads\Models;

use App\Lib\StandardLib\Tools\TypeSafeObjectStorage;

/**
 * Class AdsContainer
 *
 * @package App\Lib\Ads\Models
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class AdsContainer extends TypeSafeObjectStorage
{
    /**
     * PaymentMethodsContainer constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setContainerType(Ad::class, self::CONCRETE);
        parent::__construct($data);
    }
}
