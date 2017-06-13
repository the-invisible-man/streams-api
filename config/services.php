<?php

use App\Lib\Ads\AdsService;
use App\Lib\Streams\StreamsService;

return [

    /*
    |--------------------------------------------------------------------------
    | Core Application Services
    |--------------------------------------------------------------------------
    |
    | These services are coupled with the main application. Most of
    | the services in this list were designed around the domain
    | logic of the system. You can do simple things such as
    | enable and disable caching.
    |
    */

    AdsService::class => [
        'cache'         => env('SERVICE_ADS_CACHE', true),
        'cache_ttl'     => env('SERVICE_ADS_CACHE_TTL', 1440),
        'bail_if_down'  => env('SERVICE_ADS_BAIL', false)
    ],

    StreamsService::class => [
        'cache'     => env('SERVICE_STREAMS_CACHE', false),
        'cache_ttl' => env('SERVICE_STREAMS_CACHE_TTL', 1440)
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

];
