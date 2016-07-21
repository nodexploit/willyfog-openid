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
            'host'      => 'localhost',
            'name'      => 'willyfog_db',
            'username'  => 'root',
            'password'  => 'root'
        ]
    ],
];
