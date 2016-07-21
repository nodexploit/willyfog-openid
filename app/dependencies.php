<?php

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));

    return $logger;
};

$container['session'] = function ($c) {
    return new \App\Http\Session();
};

$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    $view = new \Slim\Views\Twig($settings['template_path']);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));
    $view->offsetSet('flash', $c->get('flash'));
    $view->offsetSet('session', $c->get('session'));

    return $view;
};

$container['pdo'] = function ($c) {
    $database = $c->get('settings')['database'];
    $dsn = "mysql:dbname=${database['name']};host=${database['host']}";

    return new \PDO($dsn, $database['username'], $database['password']);
};

$container['oauth'] = function ($c) {
    $database = $c->get('settings')['database'];

    $dsn = "mysql:dbname=${database['name']};host=${database['host']}";
    $storage = new \App\Lib\OAuth2\Pdo([
        'dsn' => $dsn,
        'username' => $database['username'],
        'password' => $database['password']
    ]);

    $grant_types = [
        'authorization_code' => new \OAuth2\OpenID\GrantType\AuthorizationCode($storage),
        'client_credentials' => new \OAuth2\GrantType\ClientCredentials($storage),
        'refresh_token' => new \OAuth2\GrantType\RefreshToken($storage, [
            'always_issue_new_refresh_token' => true
        ])
    ];

    $server = new OAuth2\Server($storage, [
        'enforce_state' => true,
        'allow_implicit' => true,
        'use_openid_connect' => true,
        'issuer' => $_SERVER['HTTP_HOST'],
    ], $grant_types);

    $publicKey  = file_get_contents(APP_PATH . '/data/pubkey.pem');
    $privateKey = file_get_contents(APP_PATH . '/data/privkey.pem');
    $key_storage = new OAuth2\Storage\Memory([ 'keys' => [
        'public_key'  => $publicKey,
        'private_key' => $privateKey,
    ]]);

    $server->addStorage($key_storage, 'public_key');

    return $server;
};
