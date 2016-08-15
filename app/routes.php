<?php

$namespace = '\App\Http\Controllers';

$app->group('', function () use ($namespace) {
    $this->get('/authorize', "$namespace\\OAuthController:authorize");
})->add($oauth);

$app->group('', function () use ($namespace) {
    $this->post('/authorize', "$namespace\\OAuthController:postAuthorize");
    $this->post('/token', "$namespace\\OAuthController:token");
    $this->post('/userInfo', "$namespace\\OAuthController:userInfo");

    $this->get('/public-key', "$namespace\\OAuthController:pubKey");
});

$app->group('/api/v1', function () use ($namespace) {
    $namespace .= '\V1';

    $this->post('/user/new', "$namespace\\UserController:create");
})->add($oauth);
