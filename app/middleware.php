<?php

$app->add(new \App\Http\Middleware\SessionMiddleware(['name' => 'session']));
$oauth = new \App\Http\Middleware\OAuthMiddleware($container);
