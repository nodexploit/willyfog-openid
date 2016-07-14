<?php
// DIC configuration

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));

    return $logger;
};

// Session
$container['session'] = function ($c) {
    return new \App\Http\Session();
};

$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

// view renderer
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

// PDO
$container['db'] = function ($c) {
    $db_settings = $c->get('settings')['database'];
    $dsn = "mysql:dbname=${db_settings['name']};host=${db_settings['host']}";
    $pdo = new \PDO($dsn, $db_settings['user'], $db_settings['password']);

    return $pdo;
};

// OAuth2
$container['oauth'] = function ($c) {
    $db_settings = $c->get('settings')['database'];

    $dsn = "mysql:dbname=${db_settings['name']};host=${db_settings['host']}";
    $storage = new OAuth2\Storage\Pdo(['dsn' => $dsn, 'username' => $db_settings['user'], 'password' => $db_settings['password']]);

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
