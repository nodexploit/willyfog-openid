<?php


namespace App\Http\Controllers;

use App\Models\User;
use OAuth2\Request as OAuthRequest;
use OAuth2\Response as OAuthResponse;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use App\Http\Response as WResponse;
use Slim\Http\Response;

class UserController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function create(Request $request, Response $response, array $args)
    {
        $server = $this->ci->get('oauth');
        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        if (!$server->validateAuthorizeRequest($oa_request, $oa_response)) {
            return WResponse::createFromOAuth($oa_response);
        } 

        return $this->ci->get('view')->render($response, 'authorize.twig', [
            'query_string' => $_SERVER['QUERY_STRING']
        ]);
    }
}
