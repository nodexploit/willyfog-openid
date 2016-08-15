<?php


namespace App\Http\Controllers\V1;

use App\Models\User;
use OAuth2\Request as OAuthRequest;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
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
        $params = $request->getParsedBody();

        $user = new User($this->ci);
        $user_id = $user->create([
            'name'      => $params['name'],
            'surname'   => $params['surname'],
            'nif'       => $params['nif'],
            'email'     => $params['email'],
            'digest'    => password_hash($params['digest'], PASSWORD_DEFAULT)
        ]);

        if ($user_id === null) {
            return $response->withJson('Oops, something went wrong while creating the user.', 409);
        }

        $last_id = $user->registerInDegree($user_id, $params['degree_id']);

        if ($last_id === null) {
            return $response->withJson('Oops, something went wrong with the degree id.', 409);
        }

        return $response->withJson('User successfully created');
    }
}
