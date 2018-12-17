<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('louderhub', 'louderhub'),

    'connections' => [
        'louderhub' => [
            'driver'        => 'mysql',
            'host'          => env('louderhub_DB_HOST'),
            'port'          => env('louderhub_DB_PORT', '3306'),
            'database'      => env('louderhub_DB_DATABASE'),
            'username'      => env('louderhub_DB_USERNAME', 'asenses'),
            'password'      => env('louderhub_DB_PASSWORD', ''),
            'unix_socket'   => env('louderhub_DB_SOCKET', ''),
            'charset'       => 'utf8mb4',
            'collation'     => 'utf8mb4_unicode_ci',
            'prefix'        => '',
            'strict'        => false,
            'engine'        => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
