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
    private $_id;

    /**
     * @var string
     */
    private $streamUrl;

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
            '_id'       => $this->_id,
            'streamUrl' => $this->streamUrl,
            'captions'  => $this->captions
        ];
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return (string)$this->_id;
    }

    /**
     * @return string
     */
    public function getStreamUrl() : string
    {
        return (string)$this->streamUrl;
    }

    /**
     * @return array
     */
    public function getCaptions() : array
    {
        return $this->captions;
    }
}
