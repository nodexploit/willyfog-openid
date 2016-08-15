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

    public function create($params)
    {
        $stm = $this->pdo->prepare(
            'INSERT INTO user (name, surname, nif, email, digest) VALUES (?, ?, ?, ?, ?)'
        );

        $success = $stm->execute(array_values($params));

        return $success ? $this->pdo->lastInsertId() : null;
    }

    public function registerInDegree($user_id, $degree_id)
    {
        $stm = $this->pdo->prepare(
            'INSERT INTO user_enrolled_degree
             (user_id, degree_id) VALUES (?, ?)'
        );

        $success = $stm->execute([$user_id, $degree_id]);

        return $success ? $this->pdo->lastInsertId() : null;
    }
}
