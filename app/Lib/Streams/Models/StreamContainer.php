<?php

namespace App\Lib\Streams\Models;

use App\Lib\StandardLib\Tools\TypeSafeObjectStorage;

/**
 * Class StreamContainer
 *
 * @package App\Lib\Streams\Models
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class StreamContainer extends TypeSafeObjectStorage
{
    /**
     * PaymentMethodsContainer constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setContainerType(Stream::class, self::CONCRETE);
        parent::__construct($data);
    }
}
