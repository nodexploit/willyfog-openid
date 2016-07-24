<?php


namespace App\Lib\OAuth2;

class Pdo extends \OAuth2\Storage\Pdo
{
    /**
     * Pdo constructor. override default table names.
     * @param $connection
     * @param array $config
     */
    public function __construct($connection, array $config = [])
    {
        parent::__construct($connection, $config);

        $this->config = array_replace(
            $this->config,
            [
                'client_table'          => 'oauth_client',
                'access_token_table'    => 'oauth_access_token',
                'refresh_token_table'   => 'oauth_refresh_token',
                'code_table'            => 'oauth_authorization_code',
                'user_table'            => 'user',
                'jwt_table'             => 'oauth_jwt',
                'scope_table'           => 'oauth_scope'
            ]
        );
    }

    public function getUser($user_id)
    {
        $stmt = $this->db->prepare($sql = sprintf('SELECT * from %s where id=:user_id', $this->config['user_table']));
        $stmt->execute(array('user_id' => $user_id));

        if (!$userInfo = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return false;
        }

        // the default behavior is to use "username" as the user_id
        return array_merge(array(
            'user_id' => $user_id
        ), $userInfo);
    }
}
