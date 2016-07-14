<?php

$namespace = '\App\Http\Controllers';

$app->group('', function () use ($namespace) {
    $this->get('/authorize', "$namespace\\OAuthController:authorize");
    $this->post('/authorize', "$namespace\\OAuthController:postAuthorize");
    $this->post('/token', "$namespace\\OAuthController:token");
    $this->post('/userInfo', "$namespace\\OAuthController:userInfo");
});
