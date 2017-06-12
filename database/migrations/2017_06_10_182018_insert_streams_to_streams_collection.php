<?php

use MongoDB\Client;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use App\Lib\Streams\Repositories\MongoStreams;

class InsertStreamsToStreamsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $config = \Config::get('streams.repositories.' . MongoStreams::class);

        /**
         * @var Client $client
         * @var \MongoDB\Collection $collection
         */
        $client     = \App::make(Client::class);
        $collection = $client->selectCollection($config['database'], $config['collection']);
        $data       = file_get_contents(realpath(__DIR__ . '/../streams-mongoexport.json'));
        // Returns associative array instead of stdClass when second param is true.
        $data       = json_decode($data, true);

        $collection->insertMany($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no op
    }
}
