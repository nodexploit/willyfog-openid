<?php


namespace App\Models;

class User extends BaseModel
{
    public function find($email)
    {
        $stm = $this->pdo->prepare(
            'SELECT id, digest AS hashed_password
              FROM user
              WHERE email = ?'
        );
        $stm->execute([$email]);

        return $stm->fetchAll()[0];
    }
}
