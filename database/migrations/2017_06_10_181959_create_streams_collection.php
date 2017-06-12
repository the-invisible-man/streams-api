<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use App\Lib\Streams\Repositories\MongoStreams;

class CreateStreamsCollection extends Migration
{
    /**
     * @throws Exception
     */
    public function up()
    {
        $config = \Config::get('streams.repositories.' . MongoStreams::class);

        /**
         * @var \MongoDB\Database $mongo
         */
        $mongo = \App::make(\MongoDB\Client::class)->{$config['database']};

        try {
            $mongo->createCollection($config['collection'], [
                'createdAt' => 1
            ]);
        } catch (\Exception $e) {
            if ($e->getMessage() != 'collection already exists'){
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * @var \MongoDB\Database $mongo
         */
        $mongo = \App::make(\MongoDB\Client::class)->billing;

        $mongo->dropCollection('streams');
    }
}
