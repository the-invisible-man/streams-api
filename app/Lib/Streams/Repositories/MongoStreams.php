<?php

namespace App\Lib\Streams\Repositories;

use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use App\Lib\Streams\Models\Stream;
use App\Lib\Streams\Models\StreamContainer;
use App\Lib\StandardLib\Traits\ChecksArrayKeys;
use App\Lib\StandardLib\Traits\ValidatesConfig;
use App\Lib\Streams\Contracts\StreamsRepository;
use App\Lib\StandardLib\Traits\IteratesIntoArray;
use App\Lib\StandardLib\Exceptions\DataCheckException;
use App\Lib\Streams\Exceptions\StreamNotFoundException;
use App\Lib\StandardLib\Exceptions\CorruptedDataException;

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
     * @return Stream
     * @throws StreamNotFoundException
     */
    public function fetch(string $streamId) : Stream
    {
        $data = $this->collection->findOne(['_id' => $streamId]);

        if (!$data instanceof BSONDocument) {
            throw new StreamNotFoundException("Unable to fetch stream with id {$streamId} from Mongo");
        }

        return $this->hydrateOne($data);
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

    /**
     * @param BSONDocument $data
     * @return Stream
     * @throws CorruptedDataException
     */
    private function hydrateOne(BSONDocument $data) : Stream
    {
        // Unpack data into native PHP associative array.
        $data = $this->iteratorToArray($data);

        try {
            // Validate the data that we received from mongo.
            $this->hasKeys(['_id', 'streamUrl', 'captions'], $data);
        } catch (DataCheckException $e) {
            // The reason that we are catching and rethrowing under a different exception is
            // because DataCheckException is thrown by the hasKeys method from the ChecksArrayKeys
            // trait and it is meant for a general use. This error however is more specific, we have
            // bad data stored, if we rethrow under this exception the main exception handler will determine
            // the appropriate Http error code, and in addition we can programmatically respond to events when
            // we have corrupted data.
            throw new CorruptedDataException($e->getMessage());
        }

        return new Stream($data);
    }
}
