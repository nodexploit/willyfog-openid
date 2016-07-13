<?php
// Routes

$app->get('/authorize', '\App\Http\Controllers\OAuthController:authorize');
$app->post('/authorize', '\App\Http\Controllers\OAuthController:postAuthorize');
$app->post('/token', '\App\Http\Controllers\OAuthController:token');
