<?php

use App\Lib\Ads\Providers\NanoScaleMock;

return [

    /*
    |--------------------------------------------------------------------------
    | Advertisement Configuration
    |--------------------------------------------------------------------------
    |
    | From this file we can set the default ad service provider that
    | gets returned for every stream.
    |
    */

    'default'   => NanoScaleMock::class,

    'providers' => [

        NanoScaleMock::class => [
            'api-url' => 'https://gruesome-rate-3945.nanoscaleapi.io/v1/codingchallenge/ads/'
        ]

    ]
];
