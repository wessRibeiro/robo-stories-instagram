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

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'    => 'sqlite',
            'database'  => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'    => '',
        ],

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST', '127.0.0.1'),
            'port'        => env('DB_PORT', '3306'),
            'database'    => env('DB_DATABASE', 'forge'),
            'username'    => env('DB_USERNAME', 'forge'),
            'password'    => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
        'missaoveja' => [
            'driver'        => 'mysql',
            'host'          => env('DB_HOST'),
            'port'          => env('DB_PORT', '3306'),
            'database'      => env('DB_DATABASE'),
            'username'      => env('DB_USERNAME', 'asenses'),
            'password'      => env('DB_PASSWORD', ''),
            'unix_socket'   => env('DB_SOCKET', ''),
            'charset'       => 'utf8mb4',
            'collation'     => 'utf8mb4_unicode_ci',
            'prefix'        => '',
            'strict'        => false,
            'engine'        => null,
        ],
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
        'spoktoberfest' => [
            'driver'        => 'mysql',
            'host'          => env('spoktoberfest_DB_HOST'),
            'port'          => env('spoktoberfest_DB_PORT', '3306'),
            'database'      => env('spoktoberfest_DB_DATABASE'),
            'username'      => env('spoktoberfest_DB_USERNAME', 'asenses'),
            'password'      => env('spoktoberfest_DB_PASSWORD', ''),
            'unix_socket'   => env('spoktoberfest_DB_SOCKET', ''),
            'charset'       => 'utf8mb4',
            'collation'     => 'utf8mb4_unicode_ci',
            'prefix'        => '',
            'strict'        => false,
            'engine'        => null,
        ],
        'nissin' => [
            'driver' => 'mysql',
            'host'          => env('nissin_DB_HOST'),
            'port'          => env('nissin_DB_PORT', '3306'),
            'database'      => env('nissin_DB_DATABASE'),
            'username'      => env('nissin_DB_USERNAME', 'asenses'),
            'password'      => env('nissin_DB_PASSWORD', ''),
            'unix_socket'   => env('nissin_DB_SOCKET', ''),
            'charset'       => 'utf8mb4',
            'collation'     => 'utf8mb4_unicode_ci',
            'prefix'        => '',
            'strict'        => false,
            'engine'        => null,
        ],
        'gallo' => [
            'driver'        => 'mysql',
            'host'          => env('gallo_DB_HOST'),
            'port'          => env('gallo_DB_PORT', '3306'),
            'database'      => env('gallo_DB_DATABASE'),
            'username'      => env('gallo_DB_USERNAME', 'asenses'),
            'password'      => env('gallo_DB_PASSWORD', ''),
            'unix_socket'   => env('gallo_DB_SOCKET', ''),
            'charset'       => 'utf8mb4',
            'collation'     => 'utf8mb4_unicode_ci',
            'prefix'        => '',
            'strict'        => false,
            'engine'        => null,
        ],
        'pgsql' => [
            'driver'        => 'pgsql',
            'host'          => env('DB_HOST', '127.0.0.1'),
            'port'          => env('DB_PORT', '5432'),
            'database'      => env('DB_DATABASE', 'forge'),
            'username'      => env('DB_USERNAME', 'forge'),
            'password'      => env('DB_PASSWORD', ''),
            'charset'       => 'utf8',
            'prefix'        => '',
            'schema'        => 'public',
            'sslmode'       => 'prefer',
        ],

        'sqlsrv' => [
            'driver'        => 'sqlsrv',
            'host'          => env('DB_HOST', 'localhost'),
            'port'          => env('DB_PORT', '1433'),
            'database'      => env('DB_DATABASE', 'forge'),
            'username'      => env('DB_USERNAME', 'forge'),
            'password'      => env('DB_PASSWORD', ''),
            'charset'       => 'utf8',
            'prefix'        => '',
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
