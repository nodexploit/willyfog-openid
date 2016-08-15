<?php


namespace App\Http\Controllers;

use App\Http\Session;
use App\Models\User;
use Interop\Container\ContainerInterface;
use OAuth2\Request as OAuthRequest;
use OAuth2\Response as OAuthResponse;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Http\Response as WResponse;

class OAuthController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function authorize(Request $request, Response $response, array $args)
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

    public function postAuthorize(Request $request, Response $response, array $args)
    {
        $server = $this->ci->get('oauth');
        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();
        
        $email = $request->getParsedBodyParam('email');
        $password = $request->getParsedBodyParam('password');

        $user = new User($this->ci);
        $user = $user->find($email);

        if (empty($user)) {
            $this->ci->get('session')->set('email', $email);
            $this->ci->get('session')->set('password', $password);
            $this->ci->get('flash')->addMessage('error', 'Provided user does not exist');

            return $response->withRedirect("/authorize?${_SERVER['QUERY_STRING']}");
        }

        $is_authorized = password_verify($password, $user['hashed_password']);

        if (!$is_authorized) {
            $this->ci->get('session')->set('email', $email);
            $this->ci->get('session')->set('password', $password);
            $this->ci->get('flash')->addMessage('error', 'The provided email/password is not correct.');

            return $response->withRedirect("/authorize?${_SERVER['QUERY_STRING']}");
        }

        $server->handleAuthorizeRequest($oa_request, $oa_response, $is_authorized, $user['id']);

        Session::destroy();

        return WResponse::createFromOAuth($oa_response);
    }

    public function token(Request $request, Response $response, array $args)
    {
        $server = $this->ci->get('oauth');

        return WResponse::createFromOAuth(
            $server->handleTokenRequest(OAuthRequest::createFromGlobals())
        );
    }

    public function userInfo(Request $request, Response $response, array $args)
    {
        $server = $this->ci->get('oauth');
        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        $server->handleUserInfoRequest($oa_request, $oa_response);

        return WResponse::createFromOAuth($oa_response);
    }

    /**
     * TODO: handle absence of pubkey
     *
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function pubKey(Request $request, Response $response, array $args)
    {
        $key = file_get_contents(APP_PATH . '/data/pubkey.pem');

        return $response->write($key);
    }
}
