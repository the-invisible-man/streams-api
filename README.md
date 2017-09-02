# Streams API

The Streams API follows the guidelines set forth by the Discovery Digital [coding challenge](https://github.com/discovery-digital/svc-codingchallenge/blob/master/streams_api.md). API documentation is available at [Apiary](http://docs.streamsapi.apiary.io/).

## Dependencies

* PHP 7.0
* Redis
* Composer
* Pear (PHP package manager)
* MongoDB
* A database for keeping track of the migrations, MySQL should be sufficient.

## Installing and Configuring

This project requires the MongoDB C extension for PHP distributed by Pear before you can install the composer dependencies. The extension can be installed with the following command.
```sh
$ sudo pecl install mongodb
```

All code dependencies can be installed with composer by entering the project's root directory and executing the following:
```sh
$ composer install
```

All common configuration changes, including the MongoDB connection config and the Redis config, can be done from the `.env` located in the project's root. In a real world scenario the `.env` file would be added to the .gitignore file but given the nature of this challenge the `.env` file has been added to the repo to ease the setup of this project.

Additional configurations can be changed from the `./config` folder in the root of the application. Configuration for the core services can be found at `./config/services.php`.

Once the MySQL database connection and the Mongo connection have been configured, it's time to run the migrations that create the mongo database and collection:

```sh
$ php artisan migrate
```

## Services

The application is broken down into services, these services have been decoupled around the domain logic for the system. The two main services found in this repo are the `StreamingService` and the `AdsService`. Both services implement the repository pattern to retrieve information.

Inside each service folder you will also find a [Service Provider](https://laravel.com/docs/5.4/providers). This allows all services, and their dependencies to be injected by Laravel's IoC pattern (Laravel's Service Container). 

All services can be found in: `./app/Lib`.

*A note on the AdsService*: In order to avoid service disruptions when an Ad provider is down, we can choose to ignore ad failures and return the stream data. This mode is enabled by default and can be changed from the `.env` file by updating the SERVICE_ADS_BAIL variable to `true` or `false`.

*A note on the StreamsService*: Results for all streams are not paginated, although pagination would be a good idea specially considering that under a real world scenario there would be more than only 3 streams.

### Standard Lib

A collection of classes and helper methods that I've put together over time to facilitate development with the Laravel Framework. This can be found insde the `Services` folder, although this is not considered a "service" per se.

## Caching

A few of the assumptions that were made when building this API:

* Ad data is always the same for a specific Stream ID.
* This API will be under heavy load from all clients (Mobile Apps, SPA, TV devices)

With these two assumptions in mind the system was designed to cache redundant information. Both the `StreamService` and the `AdService` have the ability to cache their responses. This cache can be turned off for just one service or for both services from the `.env` file.

### What gets cached?

By default only the `AdsService` caches its responses on a per stream id basis. One could enable the cache on the `StreamService` to avoid unpacking complex mongo structures per call, but keep in mind that given that the advertisement data is considered part of the Stream object, such data will also be cached even if the cache is off for the `AdsService`.

### Cache Warmer

Because multiple API calls to the Ad service can become costly when fetching all streams, this app includes a cache warmer which preloads all ad data for all the streams. You can start the cache warmer by executing the following command from the app's root:

```sh
$ php artisan cache:warmup
```

## Logging

Logging is done using the PSR compliant Laravel logger. In addition a wrapper for this logger has been adapted so that we can add contextual information for diagnosing failures. One key component is the request UUID. At the start of every request the same instance of the class `RequestIdentifier` from the `StandardLib` is injected into the services, and the logger also receives the request's UUID. This allows us to isolate the flow that a failed request went through if we were to use a tool like Kibana for looking at logs. Furthermore each service's log class will include the service name where the log line was written from.

Sample log output:

```
[2017-06-12 09:32:49] local.INFO: [CacheService:ads_service] Building cache key: ads_service[5938b99cb6906eb1fbaf1f1c] {"_request":{"uid":"593e5fa7344da"}} 
[2017-06-12 09:36:39] local.INFO: [StreamsService] Fetching fresh stream object with id 5938b99cb6906eb1fbaf1f1c from repository. {"_request":{"uid":"593e60a772324"}} 
[2017-06-12 09:36:39] local.INFO: [CacheService:ads_service] Building cache key: ads_service[5938b99cb6906eb1fbaf1f1c] {"_request":{"uid":"593e60a772324"}} 
[2017-06-12 09:36:39] local.INFO: [AdsService] Fetching fresh advertisement object with id 5938b99cb6906eb1fbaf1f1c from repository. {"_request":{"uid":"593e60a772324"}} 
[2017-06-12 09:36:39] local.INFO: [AdsService] Caching is enabled, adding object to cache. {"_request":{"uid":"593e60a772324"}} 
[2017-06-12 09:36:39] local.INFO: [CacheService:ads_service] Building cache key: ads_service[5938b99cb6906eb1fbaf1f1c] {"_request":{"uid":"593e60a772324"}} 
[2017-06-12 09:36:39] local.INFO: [StreamsService] Caching is disabled {"_request":{"uid":"593e60a772324"}} 
[2017-06-12 09:36:41] local.INFO: [StreamsService] Fetching fresh stream object with id 5938b99cb6906eb1fbaf1f1c from repository. {"_request":{"uid":"593e60a9a7c47"}} 
[2017-06-12 09:36:41] local.INFO: [CacheService:ads_service] Building cache key: ads_service[5938b99cb6906eb1fbaf1f1c] {"_request":{"uid":"593e60a9a7c47"}} 
[2017-06-12 09:36:41] local.INFO: [AdsService] Fetching advertisement data for stream with id 5938b99cb6906eb1fbaf1f1c from cache. {"_request":{"uid":"593e60a9a7c47"}} 
[2017-06-12 09:36:41] local.INFO: [CacheService:ads_service] Building cache key: ads_service[5938b99cb6906eb1fbaf1f1c] {"_request":{"uid":"593e60a9a7c47"}} 
[2017-06-12 09:36:41] local.INFO: [StreamsService] Caching is disabled {"_request":{"uid":"593e60a9a7c47"}} 
```

## Tests

A test suite has been included with this repo to validate API responses, as well as single unit failures.
