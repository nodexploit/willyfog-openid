<?php


namespace App\Http\Controllers;

use App\Http\Response;
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

        return Response::createFromOAuth(
            $server->handleTokenRequest(OAuthRequest::createFromGlobals())
        );
    }

    public function authorize($request, $response, $args)
    {
        $server = $this->ci->get('oauth');

        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        if (!$server->validateAuthorizeRequest($oa_request, $oa_response)) {
            return Response::createFromOAuth($oa_response);
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

        return Response::createFromOAuth($oa_response);
    }
}
