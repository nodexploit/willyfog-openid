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
            return $response->withJson([
                'status' => 'Oops, something went wrong with the user data',
                'messages' => $user->getMessages()
            ]);
        }

        if ($params['degree_id'] != 0) { // Student registration
            $last_id = $user->registerInDegree($user_id, $params['degree_id']);

            if ($last_id === null) {
                return $response->withJson([
                    'status' => 'Oops, something went wrong with the degree id',
                    'messages' => $user->getMessages()
                ]);
            }
        } else {
            if ($params['centre_id'] != 0) { // Coordinator needs to be related with center
                $last_id = $user->registerInCentre($user_id, $params['centre_id']);

                if ($last_id === null) {
                    return $response->withJson([
                        'status' => 'Oops, something went wrong with the centre id',
                        'messages' => $user->getMessages()
                    ]);
                }
            }
        }

        $last_id = $user->assignRole($user_id, $params['role_id']);

        if ($last_id === null) {
            return $response->withJson([
                    'status' => 'Oops, something went wrong with the role id',
                    'messages' => $user->getMessages()
                ]);
        }

        return $response->withJson([
            'status' => 'Success',
            'messages' => 'User successfully created'
        ]);
    }
}
