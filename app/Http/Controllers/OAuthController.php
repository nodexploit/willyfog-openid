<?php


namespace App\Http\Controllers;

use App\Http\Response;
use App\Http\Session;
use App\Models\User;
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

    public function authorize($request, $response, $args)
    {
        $server = $this->ci->get('oauth');
        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        if (!$server->validateAuthorizeRequest($oa_request, $oa_response)) {
            return Response::createFromOAuth($oa_response);
        }

        return $this->ci->get('view')->render($response, 'authorize.twig', [
            'query_string' => $_SERVER['QUERY_STRING']
        ]);
    }

    public function postAuthorize($request, $response, $args)
    {
        $server = $this->ci->get('oauth');
        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();
        
        $email = $request->getParsedBodyParam('email');
        $password = $request->getParsedBodyParam('password');

        $user = new User($this->ci);
        $user = $user->find($email);

        if (empty($user)) {
            $this->ci->get('session')->set('username', $email);
            $this->ci->get('session')->set('password', $password);
            $this->ci->get('flash')->addMessage('error', 'Provided user does not exist');

            return $response->withRedirect("/authorize?${_SERVER['QUERY_STRING']}");
        }

        $is_authorized = password_verify($password, $user['hashed_password']);

        if (!$is_authorized) {
            $this->ci->get('session')->set('username', $email);
            $this->ci->get('session')->set('password', $password);
            $this->ci->get('flash')->addMessage('error', 'The provided email/password is not correct.');

            return $response->withRedirect("/authorize?${_SERVER['QUERY_STRING']}");
        }

        $server->handleAuthorizeRequest($oa_request, $oa_response, $is_authorized, $user['id']);

        Session::destroy();

        return Response::createFromOAuth($oa_response);
    }

    public function token($request, $response, $args)
    {
        $server = $this->ci->get('oauth');

        return Response::createFromOAuth(
            $server->handleTokenRequest(OAuthRequest::createFromGlobals())
        );
    }

    public function userInfo($request, $response, $args)
    {
        $server = $this->ci->get('oauth');
        $oa_request = OAuthRequest::createFromGlobals();
        $oa_response = new OAuthResponse();

        $server->handleUserInfoRequest($oa_request, $oa_response);

        return Response::createFromOAuth($oa_response);
    }

    /**
     * TODO: handle absence of pubkey
     *
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function pubKey($request, $response, $args)
    {
        $key = file_get_contents(APP_PATH . '/data/pubkey.pem');

        return $response->write($key);
    }
}
