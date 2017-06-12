<?php

namespace App\Lib\Streams\Models;

use Illuminate\Contracts\Support\Arrayable;
use App\Lib\StandardLib\Hydrators\HydratesObject;

/**
 * Class Stream
 *
 * @package App\Lib\Streams\Models
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class Stream implements Arrayable
{
    use HydratesObject;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $streamurl;

    /**
     * @var array
     */
    private $captions;

    /**
     * Stream constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->hydrateProperties($data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_id'       => $this->getId(),
            'streamUrl' => $this->getStreamUrl(),
            'captions'  => $this->getCaptions()
        ];
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return (string)$this->id;
    }

    /**
     * @return string
     */
    public function getStreamUrl() : string
    {
        return (string)$this->streamurl;
    }

    /**
     * @return array
     */
    public function getCaptions() : array
    {
        return $this->captions;
    }
}
