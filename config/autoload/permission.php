<?php

use Mine\Kernel\Casbin\Adapters\DatabaseAdapter;

return [
    /*
    * Casbin model setting.
    */
    'model' => [
        // Available Settings: "file", "text"
        'type' => 'file',

        'path' => __DIR__ . '/casbin/rbac-model.conf',

        'text' => '',
    ],

    /*
    * Casbin adapter .
    */
    'adapter' => DatabaseAdapter::class,

    /*
    * Database setting.
    */
    'database' => [
        // Database connection for following tables.
        'connection' => 'default',

        // Rule table name.
        'table' => 'rules',
    ],

    'log' => [
        // changes whether Lauthz will log messages to the Logger.
        'enabled' => false,
    ],
];