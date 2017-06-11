<?php

namespace App\Lib\Streams\Repositories;

use MongoDB\Client;
use Illuminate\Log\Writer;
use MongoDB\Model\BSONDocument;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use App\Lib\Streams\Contracts\StreamsRepository;

/**
 * Class MongoStreams
 *
 * @package App\Lib\Streams
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class MongoStreams implements StreamsRepository
{
    use ValidatesConfig;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    /**
     * @var \MongoDB\Collection
     */
    private $collection;

    /**
     * @var Writer
     */
    private $log;

    /**
     * MongoStreams constructor.
     * @param array $config
     * @param Client $client
     * @param Writer $log
     */
    public function __construct(array $config, Client $client, Writer $log)
    {
        $this->config       = $this->validateConfig($config);
        $this->client       = $client;
        $this->collection   = $this->client->selectCollection($this->config['database'], $this->config['collection']);
        $this->log          = $log;
    }

    /**
     * @return array
     */
    public function getRequiredConfig() : array
    {
        return [
            'database',
            'collection'
        ];
    }

    /**
     * This can be a costly operation for large complex structures, that's why caching is important
     * @param \ArrayObject $object
     * @param bool         $recursive
     * @return array
     */
    private function iteratorToArray(\ArrayObject $object, bool $recursive = true) : array
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
