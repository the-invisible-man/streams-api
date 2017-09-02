<?php

use App\Lib\Streams\Repositories\MongoStreams;

return [

    /*
    |--------------------------------------------------------------------------
    | Streams
    |--------------------------------------------------------------------------
    |
    | Some of the main configurations for the streaming API can be found here.
    |
    */

    'default'   => env('DEFAULT_STREAM_REPO', MongoStreams::class),

    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | Register data sources for streams.
    |
    */

    'repositories' => [

        MongoStreams::class => [
            'database'      => 'streams',
            'collection'    => 'streams'
        ]

    ]
];
