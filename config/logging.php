<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'default' => env('LOG_CHANNEL', 'daily'),

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace'   => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    'channels' => [

        'daily' => [
            'driver'              => 'daily',
            'path'                => storage_path('logs/laravel.log'),
            'level'               => env('LOG_LEVEL', 'debug'),
            'days'                => 30,
            'replace_placeholders'=> true,
        ],

        'errors' => [
            'driver'              => 'daily',
            'path'                => storage_path('logs/errors.log'),
            'level'               => 'error',
            'days'                => 60,
            'replace_placeholders'=> true,
        ],


        'audit' => [
            'driver'              => 'daily',
            'path'                => storage_path('logs/audit.log'),
            'level'               => 'info',
            'days'                => 365,
            'replace_placeholders'=> true,
        ],

        'stack' => [
            'driver'            => 'stack',
            'channels'          => explode(',', env('LOG_STACK', 'daily,errors')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver'              => 'single',
            'path'                => storage_path('logs/laravel.log'),
            'level'               => env('LOG_LEVEL', 'debug'),
            'replace_placeholders'=> true,
        ],

        'stderr' => [
            'driver'     => 'monolog',
            'level'      => env('LOG_LEVEL', 'debug'),
            'handler'    => StreamHandler::class,
            'handler_with' => ['stream' => 'php://stderr'],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],
    ],
];