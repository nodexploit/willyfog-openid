<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'database' => [
            'host'      => DB_HOST,
            'name'      => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASS
        ]
    ],
];
