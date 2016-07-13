<?php


namespace App\Http\Controllers;

use Interop\Container\ContainerInterface;
use OAuth2\Request as OAuthRequest;
use OAuth2\Response as OAuthResponse;

class OAuthController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function token($request, $response, $args)
    {
        $server = $this->ci->get('oauth');

        return $server->handleTokenRequest(OAuthRequest::createFromGlobals())->send();
    }

    public function authorize($request, $response, $args)
    {
        $server = $this->ci->get('oauth');

        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        if (!$server->validateAuthorizeRequest($oa_request, $oa_response)) {
            $oa_response->send();
            die;
        }

        return $this->ci->get('view')->render($response, 'authorize.phtml', [
            'query_string' => $_SERVER['QUERY_STRING']
        ]);
    }

    public function postAuthorize($request, $response, $args)
    {
        $server = $this->ci->get('oauth');

        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        $is_authorized = ($_POST['authorized'] === 'yes');

        $server->handleAuthorizeRequest($oa_request, $oa_response, $is_authorized);

        return $response->withHeader('Location', $oa_response->getHttpHeader('Location'));
    }
}