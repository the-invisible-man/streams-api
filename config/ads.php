<?php

use App\Lib\Ads\Providers\NanoScaleMock;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'default'   => NanoScaleMock::class,

    'providers' => [

        NanoScaleMock::class => [
            'api-url' => 'https://gruesome-rate-3945.nanoscaleapi.io/v1/codingchallenge/ads/'
        ]

    ]
];