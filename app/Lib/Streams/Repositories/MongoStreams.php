<?php

namespace App\Lib\Streams\Repositories;

use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use App\Lib\Streams\Models\StreamContainer;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use App\Lib\Streams\Contracts\StreamsRepository;
use App\Lib\StandardLib\Traits\IteratesIntoArray;
use App\Lib\Streams\Exceptions\StreamNotFoundException;

/**
 * Class MongoStreams
 *
 * @package App\Lib\Streams
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class MongoStreams implements StreamsRepository
{
    use ValidatesConfig, ChecksArrayKeys, IteratesIntoArray;

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
     * MongoStreams constructor.
     * @param array $config
     * @param Client $client
     */
    public function __construct(array $config, Client $client)
    {
        $this->config       = $this->validateConfig($config);
        $this->client       = $client;
        $this->collection   = $this->client->selectCollection($this->config['database'], $this->config['collection']);
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
     * @param string $streamId
     * @return array
     * @throws StreamNotFoundException
     */
    public function fetch(string $streamId) : array
    {
        $data = $this->collection->findOne(['_id' => $streamId]);

        if (!$data instanceof BSONDocument) {
            throw new StreamNotFoundException("Unable to fetch stream with id {$streamId} from Mongo");
        }

        // Unpack data into native PHP associative array.
        return $this->iteratorToArray($data);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return StreamContainer
     */
    public function all(int $offset = 0, int $limit = 10) : StreamContainer
    {
        $documents = $this->collection->find()->skip($offset)->limit($limit);

    }
}
